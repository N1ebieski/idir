<?php

namespace N1ebieski\IDir\Seeds\PHPLD\Jobs;

use Exception;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
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
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use N1ebieski\IDir\Models\Category\Dir\Category;

class DirsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected const MAX_TAGS = 50000;

    /**
     * Undocumented variable
     *
     * @var Collection
     */
    protected $items;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $userLastId;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $groupLastId;

    /**
     * Undocumented variable
     *
     * @var int
     */
    protected $fieldLastId;

    /**
     * Undocumented variable
     *
     * @var Dir
     */
    protected $dir;

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
        Collection $items,
        int $userLastId,
        int $groupLastId,
        int $fieldLastId
    ) {
        $this->items = $items;

        $this->userLastId = $userLastId;
        $this->groupLastId = $groupLastId;
        $this->fieldLastId = $fieldLastId;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function handle() : void
    {
        $fields = DB::connection('import')->table('submit_item')->where('IS_DEFAULT', 0)->get();

        $defaultStats = Stat::all();
        $countTags = Tag::count();

        $this->items->each(function ($item) use ($fields, $defaultStats, $countTags) {
            if (!$this->verify($item)) {
                return;
            }

            DB::transaction(function () use ($item, $fields, $defaultStats, $countTags) {
                $dir = Dir::make();

                $dir->id = $item->ID;
                $dir->title = strip_tags(utf8_decode($item->TITLE));
                $dir->content_html = utf8_decode($item->DESCRIPTION);
                $dir->content = utf8_decode($item->DESCRIPTION);
                $dir->status = $item->STATUS === 2 ?
                    Dir::ACTIVE
                    : Dir::INACTIVE;
                $dir->url = strtolower($item->URL);
                $dir->privileged_at = $item->EXPIRY_DATE !== null && $item->EXPIRY_DATE !== '0000-00-00 00:00:00' ?
                    Carbon::parse($item->EXPIRY_DATE)->subYear()
                    : null;
                $dir->privileged_to = $item->EXPIRY_DATE !== null && $item->EXPIRY_DATE !== '0000-00-00 00:00:00' ?
                    $item->EXPIRY_DATE
                    : null;
                $dir->created_at = $item->DATE_ADDED;
                $dir->updated_at = $item->DATE_MODIFIED;

                $dir->group()->associate(
                    !empty($item->LINK_TYPE) && Group::find($this->groupLastId + $item->LINK_TYPE) !== null ?
                        $this->groupLastId + $item->LINK_TYPE
                        : Group::DEFAULT
                );

                $dir->user()->associate(
                    !empty($item->OWNER_ID) && User::find($this->userLastId + $item->OWNER_ID) !== null ?
                        $this->userLastId + $item->OWNER_ID
                        : null
                );

                $dir->save();

                $dir->stats()->attach([
                    $defaultStats->firstWhere('slug', 'view')->id => [
                        'value' => $item->HITS
                    ]
                ]);

                if ($countTags < static::MAX_TAGS) {
                    $keywords = Config::get('icore.tag.normalizer') !== null ?
                        Config::get('icore.tag.normalizer')(utf8_decode($item->META_KEYWORDS))
                        : utf8_decode($item->META_KEYWORDS);

                    $dir->tag(
                        collect(explode(',', $keywords))
                            ->filter(function ($item) {
                                return !is_null($item) && strlen($item) <= 30;
                            })
                    );
                }

                if ($item->RATE_TOTAL > 0) {
                    $dir->ratings()->create([
                        'rating' => $item->RATE/2
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

                    $dir->fields()->attach($ids ?? []);
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
     * Undocumented function
     *
     * @param object $item
     * @return boolean
     */
    protected function verify(object $item) : bool
    {
        return Dir::where('id', $item->ID)
            ->orWhere('url', strtolower($item->URL))->first() === null;
    }
}
