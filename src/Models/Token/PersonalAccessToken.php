<?php

namespace N1ebieski\IDir\Models\Token;

use N1ebieski\ICore\Models\Token\PersonalAccessToken as BasePersonalAccessToken;

class PersonalAccessToken extends BasePersonalAccessToken
{
    // Configration

    /**
     * @var array
     */
    public static $abilities = [
        'api.groups.*',
        'api.groups.view',
        'api.dirs.*',
        'api.dirs.view',
        'api.dirs.create',
        'api.dirs.status',
        'api.dirs.edit',
        'api.dirs.delete'
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        static::$abilities = array_merge(parent::$abilities, static::$abilities);

        parent::__construct($attributes);
    }
}
