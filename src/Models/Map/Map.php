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

namespace N1ebieski\IDir\Models\Map;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;

/**
 * N1ebieski\IDir\Models\Map\Map
 *
 * @property int $id
 * @property int $model_id
 * @property string $model_type
 * @property string $lat
 * @property string $long
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $poli
 * @method static \Illuminate\Database\Eloquent\Builder|Map newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map poli()
 * @method static \Illuminate\Database\Eloquent\Builder|Map poliType()
 * @method static \Illuminate\Database\Eloquent\Builder|Map query()
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereLong($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Map extends Model
{
    use HasPolymorphic;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'lat',
        'long'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'model_id' => 'integer',
        'lat' => 'decimal:14',
        'long' => 'decimal:14',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
