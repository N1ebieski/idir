<?php

namespace N1ebieski\IDir\Database\Factories\User;

use N1ebieski\IDir\Models\User;
use N1ebieski\ICore\Database\Factories\User\UserFactory as BaseUserFactory;

class UserFactory extends BaseUserFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;
}
