<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog\Jobs;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use InvalidArgumentException;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Link;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use N1ebieski\ICore\Models\Tag\Tag;
use N1ebieski\ICore\Models\Stat\Stat;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\Models\Region\Region;
use N1ebieski\IDir\ValueObjects\Dir\Url;
use N1ebieski\IDir\Models\Field\Dir\Field;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\ICore\ValueObjects\Link\Type;
use N1ebieski\IDir\ValueObjects\Group\Slug;

class DirsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected const MAX_TAGS = 50000;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * Undocumented function
     *
     * @param Collection $items
     * @param integer $userLastId
     * @param integer $groupLastId
     * @param integer $fieldLastId
     */
    public function __construct(
        protected Collection $items,
        protected int $userLastId,
        protected int $groupLastId,
        protected int $fieldLastId
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function handle(): void
    {
        $groups = DB::connection('import')->table('groups')->get();
        $fields = DB::connection('import')->table('forms')->get();

        $defaultRegions = Region::all();
        $defaultFields = [];
        $defaultFields['map'] = Field::where('type', 'map')->first(['id']);
        $defaultFields['regions'] = Field::where('type', 'regions')->first(['id']);
        $defaultStats = Stat::all();
        $countTags = Tag::count();

        $relations = DB::connection('import')
            ->table('relations')
            ->distinct()
            ->whereExists(function ($query) {
                $query->select('id')
                    ->from('subcategories')
                    ->whereRaw('id_sub = id');
            })
            ->whereIn('id_site', $this->items->pluck('id')->toArray())
            ->select('id_sub', 'id_site')
            ->get();

        $this->items->each(function ($item) use ($relations, $groups, $fields, $defaultStats, $defaultRegions, $defaultFields, $countTags) {
            if (!$this->verify($item)) {
                return;
            }

            DB::transaction(function () use ($item, $relations, $groups, $fields, $defaultStats, $defaultRegions, $defaultFields, $countTags) {
                $dir = new Dir();

                $dir->id = $item->id;
                $dir->title = htmlspecialchars_decode($item->title);
                $dir->content_html = $this->getContentHtml($item->description);
                $dir->content = $this->getContentHtml($item->description);

                try {
                    $dir->url = $this->getUrl($item->url);
                } catch (\InvalidArgumentException $e) {
                    return;
                }

                $dir->status = $item->active;
                // @phpstan-ignore-next-line
                $dir->privileged_at = $item->date_mod !== 0 ?
                    Carbon::createFromTimestamp($item->date_mod)
                    : null;
                // @phpstan-ignore-next-line
                $dir->privileged_to = $this->getPrivilegedTo(
                    $item->date_mod,
                    ($groups->where('id', $item->group)->first()->days ?? 0)
                );
                // @phpstan-ignore-next-line
                $dir->created_at = Carbon::createFromTimestamp($item->date);
                // @phpstan-ignore-next-line
                $dir->updated_at = Carbon::createFromTimestamp($item->date);

                $group = new Group();

                $dir->group()->associate(
                    !empty($item->group) && Group::find($this->groupLastId + $item->group) !== null ?
                        $this->groupLastId + $item->group
                        : $group->makeCache()->rememberBySlug(Slug::default())
                );

                $dir->user()->associate(
                    !empty($item->user) && User::find($this->userLastId + $item->user) !== null ?
                        $this->userLastId + $item->user
                        : null
                );

                $dir->save();

                $dir->stats()->attach([
                    $defaultStats->firstWhere('slug', 'click')->id => [
                        'value' => $item->clicks
                    ],
                    $defaultStats->firstWhere('slug', 'view')->id => [
                        'value' => $item->views
                    ]
                ]);

                if ($countTags < self::MAX_TAGS) {
                    $keywords = Config::get('icore.tag.normalizer') !== null ?
                        Config::get('icore.tag.normalizer')($item->keywords)
                        : $item->keywords;

                    $dir->tag(
                        collect(explode(',', $keywords))
                            ->filter(function ($item) {
                                return !is_null($item) && strlen($item) <= 30;
                            })
                    );
                }

                if ($item->total_votes > 0) {
                    $dir->ratings()->create([
                        'rating' => ($item->total_value / $item->total_votes) / 2
                    ]);
                }

                if (!empty($item->backlink_link) && !empty($item->backlink)) {
                    $backlink = Link::where([
                        ['url', $this->getRealUrlAsString($item->backlink)],
                        ['type', Type::BACKLINK]
                    ])->first();

                    if (is_int(optional($backlink)->id)) {
                        $dirBacklink = new DirBacklink();

                        $dirBacklink->link()->associate($backlink->id);
                        $dirBacklink->dir()->associate($dir->id);
                        $dirBacklink->url = $this->getRealUrlAsString($item->backlink_link);
                        $dirBacklink->save();
                    }
                }

                if (!empty($item->url)) {
                    $dir->status()->create([
                        'attempted_at' => Carbon::now()->subDays(rand(1, 45))
                    ]);
                }

                if ($fields->isNotEmpty()) {
                    $ids = array();

                    foreach ($fields as $field) {
                        if (!empty($value = $item->{"form_{$field->id}"})) {
                            switch ($field->mod) {
                                case 1:
                                    $value = $defaultRegions->whereIn(
                                        'slug',
                                        collect(explode(',', $value))
                                            ->map(function ($item) {
                                                return Str::slug($item);
                                            })
                                            ->toArray()
                                    )
                                    ->pluck('id')->toArray();

                                    $id = $defaultFields['regions']->id;
                                    $dir->regions()->sync($value);
                                    break;

                                case 2:
                                    $loc = explode(';', $value);

                                    if (count($loc) === 2) {
                                        $value = [];
                                        $value[0] = [
                                            'lat' => $loc[0],
                                            'long' => $loc[1]
                                        ];
                                        $id = $defaultFields['map']->id;
                                        $dir->map()->updateOrCreate($value[0]);
                                    }
                                    break;

                                default:
                                    if (in_array($field->type, [3, 4])) {
                                        $value = explode(',', $value);
                                    } else {
                                        $value = htmlspecialchars_decode($value);
                                    }

                                    $id = $this->fieldLastId + $field->id;
                            }

                            // @phpstan-ignore-next-line
                            $ids[$id] = [
                                'value' => json_encode($value)
                            ];
                        }
                    }

                    $dir->fields()->attach($ids);
                }

                $dir->categories()->attach(
                    $relations->where('id_site', $item->id)->pluck('id_sub')->toArray()
                );
            });
        });
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        //
    }

    /**
     *
     * @param mixed $item
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function verify($item): bool
    {
        return Dir::where('id', $item->id)
            ->orWhere('url', $this->getRealUrlAsString($item->url))->first() === null;
    }

    /**
     *
     * @param string $url
     * @return string
     */
    protected function getRealUrlAsString(string $url): string
    {
        return trim(strtolower(Str::contains($url, 'https://') ? $url : 'http://' . $url));
    }

    /**
     *
     * @param string $url
     * @return Url
     */
    protected function getUrl(string $url): Url
    {
        return new Url($this->getRealUrlAsString($url));
    }

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return string
     */
    protected function getContentHtml(string $desc): string
    {
        $desc = preg_replace('#\[([a-z0-9=]+)\]<br />(.*?)\[/([a-z]+)\]#si', '[\\1]\\2[/\\3]', $desc);
        $desc = str_replace(array('<br /><br />[list', '<br />[list'), array('[list', '[list'), $desc);
        $desc = str_replace(array('[/list]<br /><br />', '[/list]<br />'), array('[/list]', '[/list]'), $desc);

        $desc = preg_replace('#\[url=(.*?)\](.*?)\[/url\]#si', '<a href="\\1" target="_blank" rel="noopener">\\2</a>', $desc);
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
     * @param integer $date
     * @param integer $days
     * @return string|null
     */
    protected function getPrivilegedTo(int $date, int $days): ?string
    {
        if ($date !== 0 && $days > 0) {
            return Carbon::createFromTimestamp($date)->addDays($days);

            // if ($privileged_to->greaterThan(Carbon::now())) {
            //     return $privileged_to;
            // }
        }

        return null;
    }
}
