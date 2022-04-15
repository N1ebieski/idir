<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\IDir\Repositories\UserRepo;
use N1ebieski\ICore\Models\User as BaseUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\IDir\Database\Factories\User\UserFactory;

class User extends BaseUser
{
    // Configuration

    /**
     * [protected description]
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\User::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return UserFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\User\UserFactory::new();
    }

    // Relations

    /**
     * [dirs description]
     * @return HasMany [description]
     */
    public function dirs(): HasMany
    {
        return $this->hasMany(\N1ebieski\IDir\Models\Dir::class);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return UserRepo [description]
     */
    public function makeRepo()
    {
        return App::make(UserRepo::class, ['user' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return UserFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
