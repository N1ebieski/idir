<?php

namespace N1ebieski\IDir\Http\Resources\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Resources\Json\JsonResource;
use N1ebieski\ICore\Http\Resources\User\UserResource;
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
            'url' => $this->url,
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
                $this->relationLoaded('user') && optional($request->user())->can('view', $this->resource),
                function () {
                    return [
                        'user' => App::make(UserResource::class, ['user' => $this->user])
                    ];
                }
            ),
            $this->mergeWhen(
                $this->relationLoaded('payment') && optional($request->user())->can('view', $this->resource),
                function () {
                    return [
                        'payment' => App::make(PaymentResource::class, [
                            'payment' => $this->payment,
                            'depth' => 1
                        ])
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
}
