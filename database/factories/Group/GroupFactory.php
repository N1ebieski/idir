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

namespace N1ebieski\IDir\Database\Factories\Group;

use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\Privilege;
use N1ebieski\IDir\ValueObjects\Group\Url;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use N1ebieski\IDir\ValueObjects\Group\Visible;
use N1ebieski\IDir\ValueObjects\Group\Backlink;
use N1ebieski\IDir\ValueObjects\Group\ApplyStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Group>
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst($this->faker->unique()->word),
            'desc' => $this->faker->text(300),
            'max_cats' => rand(1, 5),
            'max_models' => $this->faker->randomElement([rand(10, 50), null]),
            'max_models_daily' => $this->faker->randomElement([rand(5, 10), null]),
            'visible' => rand(Visible::INACTIVE, Visible::ACTIVE),
            'apply_status' => rand(ApplyStatus::INACTIVE, ApplyStatus::ACTIVE),
            'backlink' => rand(Backlink::INACTIVE, Backlink::OPTIONAL),
            'url' => rand(Url::INACTIVE, Url::ACTIVE)
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function applyAltDeactivation()
    {
        return $this->state(function () {
            return [
                'alt_id' => null
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function applyAltGroup()
    {
        return $this->state(function () {
            $group = new Group();

            /** @var Group */
            $group = $group->makeCache()->rememberBySlug(Slug::default());

            return [
                'alt_id' => $group->id
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function applyActive()
    {
        return $this->state(function () {
            return [
                'apply_status' => ApplyStatus::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function applyInactive()
    {
        return $this->state(function () {
            return [
                'apply_status' => ApplyStatus::INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function requiredBacklink()
    {
        return $this->state(function () {
            return [
                'backlink' => Backlink::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withoutUrl()
    {
        return $this->state(function () {
            return [
                'url' => Url::INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function requiredUrl()
    {
        return $this->state(function () {
            return [
                'url' => Url::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function maxModels()
    {
        return $this->state(function () {
            return [
                'max_models' => 1
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function maxModelsDaily()
    {
        return $this->state(function () {
            return [
                'max_models_daily' => 1
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function maxCats()
    {
        return $this->state(function () {
            return [
                'max_cats' => 1
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function public()
    {
        return $this->state(function () {
            return [
                'visible' => Visible::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function private()
    {
        return $this->state(function () {
            return [
                'visible' => Visible::INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function additionalOptionsForEditingContent()
    {
        /** @var Privilege */
        $privilege = Privilege::where('name', 'additional options for editing content')->first();

        return $this->hasAttached($privilege);
    }
}
