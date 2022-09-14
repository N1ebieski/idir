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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Services\DirBacklink\DirBacklinkService;
use N1ebieski\IDir\Repositories\DirBacklink\DirBacklinkRepo;
use N1ebieski\IDir\Database\Factories\DirBacklink\DirBacklinkFactory;

/**
 * N1ebieski\IDir\Models\DirBacklink
 *
 * @property int $id
 * @property int $dir_id
 * @property int $link_id
 * @property string $url
 * @property int $attempts
 * @property \Illuminate\Support\Carbon|null $attempted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \N1ebieski\IDir\Models\Dir $dir
 * @property-read \N1ebieski\ICore\Models\Link $link
 * @method static \N1ebieski\IDir\Database\Factories\DirBacklink\DirBacklinkFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink query()
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink whereAttemptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink whereDirId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirBacklink whereUrl($value)
 * @mixin \Eloquent
 */
class DirBacklink extends Model
{
    use HasFactory;

    // Configuration

    /**
    * The attributes that are mass assignable.
    *
    * @var array<string>
    */
    protected $fillable = ['url', 'attempts', 'attempted_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dirs_backlinks';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'attempts' => 0,
        'attempted_at' => null
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'dir_id' => 'integer',
        'link_id' => 'integer',
        'attempts' => 'integer',
        'attempted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return DirBacklinkFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\DirBacklink\DirBacklinkFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function dir(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\Dir::class);
    }

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\Link::class);
    }

    // Factories

    /**
     * [makeRepo description]
     * @return DirBacklinkRepo [description]
     */
    public function makeRepo()
    {
        return App::make(DirBacklinkRepo::class, ['dirBacklink' => $this]);
    }

    /**
     * [makeService description]
     * @return DirBacklinkService [description]
     */
    public function makeService()
    {
        return App::make(DirBacklinkService::class, ['dirBacklink' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return DirBacklinkFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
