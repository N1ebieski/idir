<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentTaggable\Taggable;
use N1ebieski\ICore\Traits\FullTextSearchable;
use N1ebieski\ICore\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\ICore\Services\TagService;
use N1ebieski\IDir\Services\DirService;

/**
 * [Dir description]
 */
class Dir extends Model
{
    use Sluggable, Taggable, FullTextSearchable, Filterable;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'content_html',
        'notes',
        'url'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = [
        'title',
        'content',
        'url'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 0,
        'notes' => null,
        'privileged_at' => null,
        'privileged_to' => null
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    // Overrides

    /**
    * Override metody z paczki Taggable bo ma hardcodowane nazwy tabel w SQL
     *
     * @param int|null $limit
     * @param int $minCount
     *
     * @return array
     */
    public function popularTags(int $limit = null, int $minCount = 1): array
    {
        $tags = app(TagService::class)->getPopularTags($limit, static::class, $minCount);

        return $tags->shuffle()->all();
    }

    /**
     * Override relacji tags, bo ma hardcodowane nazwy pól
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        $model = config('taggable.model');
        return $this->morphToMany($model, 'model', 'tags_models', 'model_id', 'tag_id')
            ->withTimestamps();
    }

    // Relations

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    /**
     * [user description]
     * @return [type] [description]
     */
    public function group()
    {
        return $this->belongsTo('N1ebieski\IDir\Models\Group');
    }

    /**
     * [payments description]
     * @return [type] [description]
     */
    public function payments()
    {
        return $this->morphMany('N1ebieski\IDir\Models\Payment\Payment', 'model');
    }

    /**
     * [categories description]
     * @return [type] [description]
     */
    public function categories()
    {
        return $this->morphToMany('N1ebieski\IDir\Models\Category\Dir\Category', 'model', 'categories_models', 'model_id', 'category_id');
    }

    // Mutators

    /**
     * [setContentAttribute description]
     * @param string $value [description]
     */
    public function setContentAttribute(string $value) : void
    {
        $this->attributes['content'] = strip_tags($value);
    }

    // Getters

    // /**
    //  * [getRepo description]
    //  * @return DirRepo [description]
    //  */
    // public function getRepo() : DirRepo
    // {
    //     return app()->make(DirRepo::class, ['dir' => $this]);
    // }

    /**
     * [getService description]
     * @return DirService [description]
     */
    public function getService() : DirService
    {
        return app()->make(DirService::class, ['dir' => $this]);
    }
}
