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

namespace N1ebieski\IDir\Http\Resources\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Models\Tag\Dir\Tag;
use N1ebieski\IDir\Models\Field\Dir\Field;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\IDir\Models\Category\Dir\Category;

/**
 * @mixin Dir
 */
class DirResource extends JsonResource
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     */
    public function __construct(Dir $dir)
    {
        parent::__construct($dir);
    }

    /**
     * Transform the resource into an array.
     *
     * @responseField id int
     * @responseField slug string
     * @responseField title string
     * @responseField short_content string A shortened version of the post without HTML formatting.
     * @responseField content string Post without HTML formatting.
     * @responseField content_html string Post with HTML formatting.
     * @responseField less_content_html string Post with HTML formatting with "show more" button.
     * @responseField notes string (available only for admin.dirs.view or owner) Additional infos for moderator.
     * @responseField url string
     * @responseField thumbnail_url string
     * @responseField sum_rating string Average rating for an entry.
     * @responseField status object (available only for api.dirs.view or owner)
     * @responseField privileged_at string (available only for api.dirs.view or owner) Start date of premium time.
     * @responseField priveleged_to string (available only for api.dirs.view or owner) End date of premium time. If null and <code>privileged_at</code> not null then premium time is unlimited.
     * @responseField created_at string
     * @responseField updated_at string
     * @responseField group object (available only for api.dirs.view or owner) Contains relationship Group.
     * @responseField user object (available only for admin.dirs.view or owner) Contains relationship User.
     * @responseField categories object[] Contains relationship Categories.
     * @responseField tags object[] Contains relationship Tags.
     * @responseField fields object[] Contains relationship custom Fields.
     * @responseField links object Contains links to resources on the website and in the administration panel.
     * @responseField meta object Paging, filtering and sorting information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'url' => $this->url->getValue(),
            'short_content' => $this->short_content,
            'content' => $this->content,
            'content_html' => $this->content_html,
            'less_content_html' => $this->less_content_html,
            $this->mergeWhen(
                optional($request->user())->can('view', $this->resource),
                function () {
                    return [
                        'notes' => $this->notes
                    ];
                }
            ),
            $this->mergeWhen(
                $this->url !== null,
                function () {
                    return [
                        'thumbnail_url' => $this->thumbnail_url
                    ];
                }
            ),
            'sum_rating' => $this->sum_rating,
            $this->mergeWhen(
                optional($request->user())->can('view', $this->resource) || optional($request->user())->can('api.dirs.view'),
                function () {
                    return [
                        'status' => [
                            'value' => $this->status->getValue(),
                            'label' => Lang::get("idir::dirs.status.{$this->status}")
                        ]
                    ];
                }
            ),
            $this->mergeWhen(
                optional($request->user())->can('view', $this->resource) || optional($request->user())->can('api.dirs.view'),
                function () {
                    return [
                        'privileged_at' => $this->privileged_at,
                        'privileged_to' => $this->privileged_to
                    ];
                }
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            $this->mergeWhen(
                $this->relationLoaded('group')
                && (optional($request->user())->can('view', $this->resource) || optional($request->user())->can('api.dirs.view')),
                function () {
                    /** @var Group|null */
                    // @phpstan-ignore-next-line
                    $group = $this->group?->setAttribute('depth', 1);

                    return [
                        'group' => $group instanceof Group ?
                            $group->makeResource()
                            : null
                    ];
                }
            ),
            $this->mergeWhen(
                $this->relationLoaded('user') && optional($request->user())->can('view', $this->resource),
                function () {
                    /** @var User|null */
                    $user = $this->user?->setAttribute('depth', 1);

                    return [
                        'user' => $user instanceof User ?
                            $user->makeResource()
                            : null
                    ];
                }
            ),
            $this->mergeWhen(
                $this->relationLoaded('categories'),
                function () {
                    /** @var Category */
                    $category = $this->categories()->make();

                    return [
                        'categories' => $category->makeResource()
                            ->collection($this->categories->map(function ($item) {
                                $item->setAttribute('depth', 1);

                                return $item;
                            }))
                    ];
                }
            ),
            $this->mergeWhen(
                $this->relationLoaded('tags'),
                function () {
                    /** @var Tag */
                    $tag = $this->tags()->make();

                    return [
                        'tags' => $tag->makeResource()
                            ->collection($this->tags->map(function ($item) {
                                $item->setAttribute('depth', 1);

                                return $item;
                            }))
                    ];
                }
            ),
            $this->mergeWhen(
                $this->relationLoaded('payment'),
                function () {
                    /** @var Payment|null */
                    $payment = $this->payment?->setAttribute('depth', 1);

                    return [
                        'payment' => $payment instanceof Payment ?
                            $payment->makeResource()
                            : null
                    ];
                }
            ),
            $this->mergeWhen(
                $this->relationLoaded('fields'),
                function () use ($request) {
                    /** @var Field */
                    $field = $this->fields()->make();

                    return [
                        'fields' => $field->makeResource()
                            ->collection(
                                $this->fields->whereIn('id', $this->group->fields->pluck('id')->toArray())
                                    ->filter(function ($item) use ($request) {
                                        if (!$item->visible->isActive() && !optional($request->user())->can('view', $this->resource)) {
                                            return false;
                                        }

                                        return true;
                                    })
                                    ->map(function ($item) {
                                        $item->setAttribute('depth', 1);

                                        return $item;
                                    })
                            )
                    ];
                }
            ),
            'links' => [
                $this->mergeWhen(
                    Config::get('icore.routes.web.enabled') === true && $this->status->isActive(),
                    function () {
                        return [
                            'web' => URL::route('web.dir.show', [$this->slug])
                        ];
                    }
                ),
                $this->mergeWhen(
                    Config::get('icore.routes.admin.enabled') === true && optional($request->user())->can('admin.dirs.view'),
                    function () {
                        return [
                            'admin' => URL::route('admin.dir.index', ['filter[search]' => 'id:"' . $this->id . '"'])
                        ];
                    }
                )
            ]
        ];
    }
}
