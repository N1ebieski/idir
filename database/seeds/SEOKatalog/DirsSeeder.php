<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Link;
use Carbon\Carbon;

class DirsSeeder extends SEOKatalogSeeder
{
    /**
     * Undocumented function
     *
     * @param string $desc
     * @return string
     */
    protected static function makeContentHtml(string $desc) : string
    {
        $desc = preg_replace('#\[([a-z0-9=]+)\]<br />(.*?)\[/([a-z]+)\]#si', '[\\1]\\2[/\\3]', $desc);
        $desc = str_replace(array('<br /><br />[list', '<br />[list'), array('[list', '[list'), $desc);
        $desc = str_replace(array('[/list]<br /><br />', '[/list]<br />'), array('[/list]', '[/list]'), $desc);
     
        $desc = preg_replace('#\[url=(.*?)\](.*?)\[/url\]#si', '<a href="\\1" target="_blank">\\2</a>', $desc);
        $desc = preg_replace('#\[b\](.*?)\[/b\]#si', '<strong>\\1</strong>', $desc);
        $desc = preg_replace('#\[i\](.*?)\[/i\]#si', '<i>\\1</i>', $desc);
        $desc = preg_replace('#\[u\](.*?)\[/u\]#si', '<u>\\1</u>', $desc);
        $desc = preg_replace('#(\[quote\]|\[quote\]<br />)(.*?)(\[/quote\]<br />|\[/quote\])#si', '<blockquote>\\1</blockquote>', $desc);
        $desc = preg_replace('#\[img\](.*?)\[/img\]#si', '<img src="\\1" />', $desc);
        $desc = preg_replace('#\[size=(.*?)\](.*?)\[/size\]#si', '<span style="font-size:\\1%;">\\2</span>', $desc);
        $desc = preg_replace('#\[list=(.*?)\](.*?)\[/list\]#si', '<ol start="\\1">\\2</ol>', $desc);
        $desc = preg_replace('#\[list\](.*?)\[/list\]#si', '<ul>\\1</ul>', $desc);
        $desc = preg_replace('#\[\*\](.*?)<br \/>#si', '<li>\\1</li>', $desc);

        $desc = str_replace(["<br />", "<br>", "<br/>"], "\r\n", $desc);
     
        return strip_tags($desc);     
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @return string
     */
    protected static function makeUrl(string $url) : string
    {
        return strpos($url, 'https://') ? $url : 'http://' . $url;
    }

    /**
     * Undocumented function
     *
     * @param integer $date
     * @param integer $days
     * @return string|null
     */
    protected static function makePrivilegedTo(int $date, int $days) : ?string
    {
        if ($date !== 0) {
            $privileged_to = Carbon::parse($date)->addDays($days);

            if ($privileged_to->greaterThan(Carbon::now())) {
                return $privileged_to;
            }
        }

        return null;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = DB::connection('import')->table('groups')->get();
        $fields = DB::connection('import')->table('forms')->get();

        DB::connection('import')
            ->table('sites')
            ->whereIn('id', function($query) {
                $query->selectRaw('MIN(id)')->from('sites')
                    ->groupBy('url');
            })
            ->orderBy('id')
            ->chunk(1000, function($items) use ($groups, $fields) {
                $relations = DB::connection('import')
                    ->table('relations')
                    ->distinct()
                    ->whereExists(function($query) {
                        $query->select('id')
                            ->from('subcategories')
                            ->whereRaw('id_sub = id');
                    })
                    ->whereIn('id_site', $items->pluck('id')->toArray())
                    ->select('id_sub', 'id_site')
                    ->get();

                $items->each(function($item) use ($groups, $relations, $fields) {

                    $dir = Dir::create([
                        'id' => $item->id,
                        'title' => $item->title,
                        'content_html' => $this->makeContentHtml($item->description),
                        'content' => $this->makeContentHtml($item->description),
                        'group_id' => $this->group_last_id + $item->group,
                        'user_id' => $item->user > 0 ? $this->user_last_id + $item->user : null,
                        'url' => $this->makeUrl($item->url),
                        'status' => $item->active,
                        'privileged_at' => $item->date_mod !== 0 ? Carbon::createFromTimestamp($item->date_mod) : null,
                        'privileged_to' => $this->makePrivilegedTo(
                            $item->date_mod, 
                            ($groups->where('id', $item->id)->first()->days ?? 0)
                        ),
                        'created_at' => Carbon::createFromTimestamp($item->date),
                        'updated_at' => Carbon::createFromTimestamp($item->date)                
                    ]);
        
                    $dir->tag(
                        collect(explode(',', $item->keywords))
                            ->filter(fn($item) => !is_null($item) && strlen($item) <= 30)
                    );

                    if (!empty($item->backlink_link)) {
                        $backlink = Link::where([
                            ['url', $this->makeUrl($item->backlink)],
                            ['type', 'backlink']
                        ])->first();

                        if (is_int(optional($backlink)->id)) {
                            $dir->backlink()->create([
                                'link_id' => $backlink->id,
                                'url' => $this->makeUrl($item->backlink_link)
                            ]);
                        }
                    }
        
                    if ($fields->isNotEmpty()) {
                        $ids = array();

                        foreach ($fields as $field) {
                            if (!empty($value = $item->{"form_{$field->id}"})) {
                                $ids[$this->field_last_id + $field->id] = [
                                    'value' => json_encode($value)
                                ];
                            }
                        }

                        $dir->fields()->attach($ids ?? []);
                    }

                    $dir->categories()->attach(
                        $relations->where('id_site', $item->id)->pluck('id_sub')->toArray()
                    );
                });
            });
    }
}
