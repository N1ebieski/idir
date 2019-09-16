<?php

namespace N1ebieski\IDir\Models\Group;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use N1ebieski\ICore\Traits\Carbonable;
use N1ebieski\ICore\Traits\Polymorphic;
use N1ebieski\IDir\Traits\Positionable;
use N1ebieski\IDir\Repositories\GroupRepo;
use N1ebieski\IDir\Services\GroupService;

/**
 * [Group description]
 */
class Group extends Model
{
    use Sluggable, Carbonable, Polymorphic, Positionable;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'desc',
        'border',
        'position',
        'max_cats',
        'max_dirs',
        'visible',
        'backlink',
        'days'
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
                'source' => 'name'
            ]
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'max_dirs' => null,
        'border' => null,
        'days' => null
    ];

    // Relations

    /**
     * [posts description]
     * @return [type] [description]
     */
    public function privileges()
    {
        return $this->belongsToMany('N1ebieski\IDir\Models\Privilege', 'groups_privileges');
    }

    /**
     * [siblings description]
     * @return [type] [description]
     */
    public function siblings()
    {
        return $this->hasMany('N1ebieski\iDir\Models\Group\Group', 'model_type', 'model_type');
    }

    // Overrides

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function(Group $group) {
            $group->position = $group->position ?? $group->getNextAfterLastPosition();
        });

        // Everytime the model's position
        // is changed, all siblings reordering will happen,
        // so they will always keep the proper order.
        static::saved(function(Group $group) {
            $group->reorderSiblings();
        });
    }

    // Getters

    /**
     * [getRepo description]
     * @return GroupRepo [description]
     */
    public function getRepo() : GroupRepo
    {
        return app()->make(GroupRepo::class, ['group' => $this]);
    }

    /**
     * [getService description]
     * @return GroupService [description]
     */
    public function getService() : GroupService
    {
        return app()->make(GroupService::class, ['group' => $this]);
    }
}
