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

namespace N1ebieski\IDir\Database\Seeders\PHPLD\Jobs;

use Exception;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use InvalidArgumentException;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use N1ebieski\ICore\Models\Tag\Tag;
use N1ebieski\ICore\Models\Stat\Stat;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\InteractsWithQueue;
use N1ebieski\IDir\ValueObjects\Dir\Url;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use Carbon\Exceptions\InvalidFormatException;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\ValueObjects\Stat\Slug as StatSlug;

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
        $fields = DB::connection('import')->table('submit_item')->where('IS_DEFAULT', 0)->get();

        $defaultStats = Stat::all();
        $countTags = Tag::count();

        $this->items->each(function ($item) use ($fields, $defaultStats, $countTags) {
            if (!$this->verify($item)) {
                return;
            }

            DB::transaction(function () use ($item, $fields, $defaultStats, $countTags) {
                $dir = new Dir();

                $dir->id = $item->ID;
                $dir->title = strip_tags(utf8_decode($item->TITLE));
                $dir->content_html = utf8_decode($item->DESCRIPTION);
                $dir->content = utf8_decode($item->DESCRIPTION);
                $dir->status = $item->STATUS === 2 ?
                    Status::active()
                    : Status::inactive();

                try {
                    $dir->url = $this->getUrl($item->URL);
                } catch (\InvalidArgumentException $e) {
                    return;
                }

                // @phpstan-ignore-next-line
                $dir->privileged_at = $this->getPrivilegedAt($item);
                // @phpstan-ignore-next-line
                $dir->privileged_to = $this->getPrivilegedTo($item);
                $dir->created_at = $item->DATE_ADDED;
                $dir->updated_at = $item->DATE_MODIFIED;

                $group = new Group();

                $dir->group()->associate(
                    !empty($item->LINK_TYPE) && Group::find($this->groupLastId + $item->LINK_TYPE) !== null ?
                        $this->groupLastId + $item->LINK_TYPE
                        : $group->makeCache()->rememberBySlug(Slug::default())
                );

                $dir->user()->associate(
                    !empty($item->OWNER_ID) && User::find($this->userLastId + $item->OWNER_ID) !== null ?
                        $this->userLastId + $item->OWNER_ID
                        : null
                );

                $dir->save();

                /** @var Stat */
                $stat = $defaultStats->firstWhere('slug', StatSlug::VIEW);

                $dir->stats()->attach([
                    $stat->id => [
                        'value' => $item->HITS
                    ]
                ]);

                if ($countTags < self::MAX_TAGS) {
                    $keywords = Config::get('icore.tag.normalizer') !== null ?
                        Config::get('icore.tag.normalizer')(utf8_decode($item->META_KEYWORDS))
                        : utf8_decode($item->META_KEYWORDS);

                    $dir->tag(
                        collect(explode(',', $keywords))
                            ->filter(function ($item) {
                                return strlen($item) <= 30;
                            })
                    );
                }

                if ($item->RATE_TOTAL > 0) {
                    $dir->ratings()->create([
                        'rating' => $item->RATE / 2
                    ]);
                }

                if (!empty($item->URL)) {
                    $dir->status()->create([
                        'attempted_at' => Carbon::now()->subDays(rand(1, 45))
                    ]);
                }

                if ($fields->isNotEmpty()) {
                    $ids = array();

                    foreach ($fields as $field) {
                        if (!property_exists($item, $field->FIELD_NAME)) {
                            continue;
                        }

                        if (!empty($value = $item->{$field->FIELD_NAME})) {
                            $value = htmlspecialchars_decode($value);
                            $id = $this->fieldLastId + $field->ID;

                            $ids[$id] = [
                                'value' => json_encode($value)
                            ];
                        }
                    }

                    $dir->fields()->attach($ids);
                }

                if (Category::find($item->CATEGORY_ID) !== null) {
                    $dir->categories()->attach([$item->CATEGORY_ID]);
                }
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
        return Dir::where('id', $item->ID)
            ->orWhere('url', mb_strtolower($item->URL))->first() === null;
    }

    /**
     *
     * @param string $url
     * @return Url
     */
    protected function getUrl(string $url): Url
    {
        return new Url(trim(mb_strtolower($url)));
    }

    /**
     *
     * @param mixed $item
     * @return null|string
     * @throws InvalidFormatException
     */
    protected function getPrivilegedAt($item): ?string
    {
        if ($item->FEATURED === 1) {
            if ($item->EXPIRY_DATE !== null && $item->EXPIRY_DATE !== '0000-00-00 00:00:00') {
                return Carbon::parse($item->EXPIRY_DATE)->subYear();
            }

            return Carbon::now()->subYears(rand(1, 5));
        }

        return null;
    }

    /**
     *
     * @param mixed $item
     * @return null|string
     */
    protected function getPrivilegedTo($item): ?string
    {
        if ($item->FEATURED === 1 && $item->EXPIRY_DATE !== null && $item->EXPIRY_DATE !== '0000-00-00 00:00:00') {
            return $item->EXPIRY_DATE;
        }

        return null;
    }
}
