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
use N1ebieski\IDir\Services\Code\CodeService;
use N1ebieski\IDir\Repositories\Code\CodeRepo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Database\Factories\Code\CodeFactory;

/**
 * N1ebieski\IDir\Models\Code
 *
 * @property int $id
 * @property int $price_id
 * @property string $code
 * @property int|null $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \N1ebieski\IDir\Models\Price $price
 * @method static \N1ebieski\IDir\Database\Factories\Code\CodeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Code newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Code newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Code query()
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code wherePriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Code whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Code extends Model
{
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'quantity'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'price_id' => 'integer',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return CodeFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Code\CodeFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function price(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\Price::class);
    }

    // Factories

    /**
     * [makeService description]
     * @return CodeService [description]
     */
    public function makeService()
    {
        return App::make(CodeService::class, ['code' => $this]);
    }

    /**
     * [makeRepo description]
     * @return CodeRepo [description]
     */
    public function makeRepo()
    {
        return App::make(CodeRepo::class, ['code' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return CodeFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
