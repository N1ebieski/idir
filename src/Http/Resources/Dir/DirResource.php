<?php

namespace N1ebieski\IDir\Http\Resources\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\User;
use N1ebieski\IDir\Models\Group;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\Models\Payment\Payment;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\ICore\Http\Resources\Tag\TagResource;
use N1ebieski\ICore\Http\Resources\User\UserResource;
use N1ebieski\IDir\Http\Resources\Group\GroupResource;
use N1ebieski\IDir\Http\Resources\Field\Dir\FieldResource;
use N1ebieski\ICore\Http\Resources\Category\CategoryResource;
use N1ebieski\IDir\Http\Resources\Payment\Dir\PaymentResource;

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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'url' => $this->url,
            $this->mergeWhen(
                $this->depth === null,
                function () use ($request) {
                    return [
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
                                        'value' => $this->status,
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
                                    'privileged_at_diff' => $this->privileged_at_diff,
                                    'privileged_to' => $this->privileged_to,
                                    'privileged_to_diff' => $this->privileged_to_diff
                                ];
                            }
                        ),
                        'created_at' => $this->created_at,
                        'created_at_diff' => $this->created_at_diff,
                        'updated_at' => $this->updated_at,
                        'updated_at_diff' => $this->updated_at_diff,
                        $this->mergeWhen(
                            $this->relationLoaded('group') && optional($request->user())->can('view', $this->resource),
                            function () {
                                return [
                                    'group' => $this->group instanceof Group ?
                                        App::make(GroupResource::class, ['group' => $this->group->setAttribute('depth', 1)])
                                        : null
                                ];
                            }
                        ),
                        $this->mergeWhen(
                            $this->relationLoaded('user') && optional($request->user())->can('view', $this->resource),
                            function () {
                                return [
                                    'user' => $this->user instanceof User ?
                                        App::make(UserResource::class, ['user' => $this->user->setAttribute('depth', 1)])
                                        : null
                                ];
                            }
                        ),
                        $this->mergeWhen(
                            $this->relationLoaded('categories'),
                            function () {
                                return [
                                    'categories' => App::make(CategoryResource::class)
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
                                return [
                                    'tags' => App::make(TagResource::class)
                                        ->collection($this->tags->map(function ($item) {
                                            $item->setAttribute('depth', 1);

                                            return $item;
                                        }))
                                ];
                            }
                        ),
                        $this->mergeWhen(
                            $this->relationLoaded('payment') && optional($request->user())->can('view', $this->resource),
                            function () {
                                return [
                                    'payment' => $this->payment instanceof Payment ?
                                        App::make(PaymentResource::class, ['payment' => $this->payment->setAttribute('depth', 1)])
                                        : null
                                ];
                            }
                        ),
                        $this->mergeWhen(
                            $this->relationLoaded('fields'),
                            function () use ($request) {
                                return [
                                    'fields' => App::make(FieldResource::class)
                                        ->collection(
                                            $this->fields->whereIn('id', $this->group->fields->pluck('id')->toArray())
                                                ->filter(function ($item) use ($request) {
                                                    if (!$item->isPublic() && !optional($request->user())->can('view', $this->resource)) {
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
                                Config::get('icore.routes.web.enabled') === true && $this->status === Dir::ACTIVE,
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
            )
        ];
    }
}
