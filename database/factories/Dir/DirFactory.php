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

namespace N1ebieski\IDir\Database\Factories\Dir;

use Carbon\Carbon;
use Illuminate\Support\Str;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Group\Slug;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class DirFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Dir>
     */
    protected $model = Dir::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $url = $this->faker->randomElement(['http', 'https']) . '://' . $this->faker->unique()->slug() . '.pl';
        $content = Str::random(350);

        return [
            // i cant use faker, because it doesnt have strict option to set min and max chars
            'title' => Str::random(rand(10, 30)),
            'content_html' => $content,
            'content' => $content,
            'url' => $url,
            'status' => rand(Status::INACTIVE, Status::ACTIVE)
        ];
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function titleSentence()
    {
        return $this->state(function () {
            return [
                'title' => $this->faker->sentence(rand(1, 3))
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function contentText()
    {
        return $this->state(function () {
            $content = $this->faker->text(350);

            return [
                'content_html' => $content,
                'content' => $content
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function active()
    {
        return $this->state(function () {
            return [
                'status' => Status::ACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function inactive()
    {
        return $this->state(function () {
            return [
                'status' => Status::INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function pending()
    {
        return $this->state(function () {
            return [
                'status' => Status::PAYMENT_INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function paidSeasonal()
    {
        return $this->state(function () {
            return [
                'status' => Status::ACTIVE,
                'privileged_at' => Carbon::now(),
                'privileged_to' => Carbon::now()->addDays(14)
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
                'url' => null
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function backlinkInactive()
    {
        return $this->state(function () {
            return [
                'status' => Status::BACKLINK_INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function statusInactive()
    {
        return $this->state(function () {
            return [
                'status' => Status::STATUS_INACTIVE
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withUser()
    {
        return $this->for(User::makeFactory()->user());
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withDefaultGroup()
    {
        $group = new Group();

        /** @var Group */
        $group = $group->makeCache()->rememberBySlug(Slug::default());

        return $this->for($group);
    }

    /**
     * Undocumented function
     *
     * @return static
     */
    public function withCategory()
    {
        return $this->hasAttached(Category::makeFactory()->active());
    }
}
