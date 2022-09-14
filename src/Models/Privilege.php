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
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Repositories\Privilege\PrivilegeRepo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * N1ebieski\IDir\Models\Privilege
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\IDir\Models\Group[] $groups
 * @property-read int|null $groups_count
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege query()
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privilege whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Privilege extends Model
{
    // Configuration

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string>
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(\N1ebieski\IDir\Models\Group::class, 'groups_privileges');
    }

    // Factories

    /**
     * [makeRepo description]
     * @return PrivilegeRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PrivilegeRepo::class, ['privilege' => $this]);
    }
}
