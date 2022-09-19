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
use N1ebieski\IDir\Services\DirStatus\DirStatusService;
use N1ebieski\IDir\Repositories\DirStatus\DirStatusRepo;
use N1ebieski\IDir\Database\Factories\DirStatus\DirStatusFactory;

/**
 * N1ebieski\IDir\Models\DirStatus
 *
 * @property int $id
 * @property int $dir_id
 * @property int $attempts
 * @property \Illuminate\Support\Carbon|null $attempted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \N1ebieski\IDir\Models\Dir $dir
 * @method static \N1ebieski\IDir\Database\Factories\DirStatus\DirStatusFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus whereAttemptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus whereAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus whereDirId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DirStatus extends Model
{
    use HasFactory;

    // Configuration

    /**
    * The attributes that are mass assignable.
    *
    * @var array<string>
    */
    protected $fillable = ['attempts', 'attempted_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dirs_status';

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
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'dir_id' => 'integer',
        'attempts' => 'integer',
        'attempted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return DirStatusFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\DirStatus\DirStatusFactory::new();
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

    // Factories

    /**
     * [makeRepo description]
     * @return DirStatusRepo [description]
     */
    public function makeRepo()
    {
        return App::make(DirStatusRepo::class, ['dirStatus' => $this]);
    }

    /**
     * [makeService description]
     * @return DirStatusService [description]
     */
    public function makeService()
    {
        return App::make(DirStatusService::class, ['dirStatus' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return DirStatusFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
