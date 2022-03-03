<?php

namespace N1ebieski\IDir\Models\Token;

use N1ebieski\ICore\Models\Token\PersonalAccessToken as BasePersonalAccessToken;

class PersonalAccessToken extends BasePersonalAccessToken
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        static::$abilities = array_merge(static::$abilities, [
            'api.dirs.*',
            'api.dirs.view',
            'api.dirs.create',
            'api.dirs.edit',
            'api.dirs.delete'
        ]);

        parent::__construct($attributes);
    }
}
