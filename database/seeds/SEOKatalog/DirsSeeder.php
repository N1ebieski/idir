<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\Models\Link;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\IDir\Models\Region\Region;

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
        $desc = preg_replace(
            '#(\[quote\]|\[quote\]<br />)(.*?)(\[/quote\]<br />|\[/quote\])#si',
            '<blockquote>\\1</blockquote>',
            $desc
        );
        $desc = preg_replace('#\[img\](.*?)\[/img\]#si', '<img src="\\1" />', $desc);
        $desc = preg_replace('#\[size=(.*?)\](.*?)\[/size\]#si', '<span style="font-size:\\1%;">\\2</span>', $desc);
        $desc = preg_replace('#\[list=(.*?)\](.*?)\[/list\]#si', '<ol start="\\1">\\2</ol>', $desc);
        $desc = preg_replace('#\[list\](.*?)\[/list\]#si', '<ul>\\1</ul>', $desc);
        $desc = preg_replace('#\[\*\](.*?)<br \/>#si', '<li>\\1</li>', $desc);

        $desc = str_replace(["<br />", "<br>", "<br/>"], "\r\n", $desc);
     
        return strip_tags(htmlspecialchars_decode($desc));
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

        $defaultRegions = Region::all();
        $defaultFields = [];
        $defaultFields['map'] = Field::where('type', 'map')->first(['id']);
        $defaultFields['regions'] = Field::where('type', 'regions')->first(['id']);

        DB::connection('import')
            ->table('sites')
            // Trick to get effect distinct by once field
            ->whereIn('id', function ($query) {
                $query->selectRaw('MIN(id)')->from('sites')
                    ->groupBy('url');
            })
            ->orderBy('id', 'desc')
            ->chunk(1000, function ($items) use ($groups, $fields, $defaultRegions, $defaultFields) {
                $relations = DB::connection('import')
                    ->table('relations')
                    ->distinct()
                    ->whereExists(function ($query) {
                        $query->select('id')
                            ->from('subcategories')
                            ->whereRaw('id_sub = id');
                    })
                    ->whereIn('id_site', $items->pluck('id')->toArray())
                    ->select('id_sub', 'id_site')
                    ->get();

                $items->each(function ($item) use ($groups, $relations, $fields, $defaultRegions, $defaultFields) {

                    $dir = Dir::create([
                        'id' => $item->id,
                        'title' => htmlspecialchars_decode($item->title),
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
        
                    $keywords = Config::get('icore.tag.normalizer') !== null ?
                        Config::get('icore.tag.normalizer')($item->keywords)
                        : $item->keywords;

                    $dir->tag(
                        collect(explode(',', $keywords))
                            ->filter(function ($item) {
                                return !is_null($item) && strlen($item) <= 30;
                            })
                    );

                    if ($item->total_votes > 0) {
                        $dir->ratings()->create([
                            'rating' => ($item->total_value/$item->total_votes)/2
                        ]);
                    }

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
        
                    if (!empty($item->url)) {
                        $dir->status()->create();
                    }

                    if ($fields->isNotEmpty()) {
                        $ids = array();

                        foreach ($fields as $field) {
                            if (!empty($value = $item->{"form_{$field->id}"})) {
                                switch ($field->mod) {
                                    case 1:
                                        $value = $defaultRegions->whereIn(
                                            'name',
                                            collect(explode(',', $value))
                                                ->map(function ($item) {
                                                    return ucfirst($item);
                                                })
                                        )
                                        ->pluck('id')->toArray();

                                        $id = $defaultFields['regions']->id;
                                        $dir->regions()->sync($value);
                                        break;

                                    case 2:
                                        $loc = explode(';', $value);
                                        $value = [];
                                        $value[0] = [
                                            'lat' => $loc[0],
                                            'long' => $loc[1]
                                        ];
                                        $id = $defaultFields['map']->id;
                                        $dir->map()->updateOrCreate($value[0]);
                                        break;

                                    default:
                                        $value = htmlspecialchars_decode($value);
                                        $id = $this->field_last_id + $field->id;
                                }

                                $ids[$id] = [
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
