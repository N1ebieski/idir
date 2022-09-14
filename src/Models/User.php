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

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\User as BaseUser;
use N1ebieski\IDir\Repositories\User\UserRepo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use N1ebieski\IDir\Database\Factories\User\UserFactory;

/**
 * N1ebieski\IDir\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string|null $ip
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property \N1ebieski\ICore\ValueObjects\User\Status $status
 * @property \N1ebieski\ICore\ValueObjects\User\Marketing $marketing
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \N1ebieski\ICore\Models\BanModel\BanModel|null $ban
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Dir[] $dirs
 * @property-read int|null $dirs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\MailingEmail\MailingEmail[] $emails
 * @property-read int|null $emails_count
 * @property-read string $created_at_diff
 * @property-read string $short_name
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Post[] $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Socialite[] $socialites
 * @property-read int|null $socialites_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Token\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|User active()
 * @method static \N1ebieski\IDir\Database\Factories\User\UserFactory factory(...$parameters)
 * @method static Builder|User filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|User filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|User filterExcept(?array $except = null)
 * @method static Builder|User filterOrderBy(?string $orderby = null)
 * @method static Builder|User filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|User filterReport(?int $report = null)
 * @method static Builder|User filterRole(?\N1ebieski\ICore\Models\Role $role = null)
 * @method static Builder|User filterSearch(?string $search = null)
 * @method static Builder|User filterStatus(?int $status = null)
 * @method static Builder|User marketing()
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static Builder|User orderBySearch(string $term)
 * @method static Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMarketing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
