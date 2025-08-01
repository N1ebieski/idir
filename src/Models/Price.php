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
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Traits\HasFilterable;
use N1ebieski\IDir\Services\Price\PriceService;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\IDir\Repositories\Price\PriceRepo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Http\Resources\Price\PriceResource;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\IDir\Database\Factories\Price\PriceFactory;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * N1ebieski\IDir\Models\Price
 *
 * @property Type $type
 * @property int $id
 * @property int $group_id
 * @property string $price
 * @property string|null $discount_price
 * @property int|null $days
 * @property string|null $code
 * @property string|null $token
 * @property int|null $number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collect<int, Code> $codes
 * @property-read int|null $codes_count
 * @property-read string|null $codes_as_string
 * @property-read string $created_at_diff
 * @property-read integer|null $discount
 * @property-read string|null $qr_as_image
 * @property-read string $regular_price
 * @property-read string $updated_at_diff
 * @property-read \N1ebieski\IDir\Models\Group $group
 * @method static \N1ebieski\IDir\Database\Factories\Price\PriceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterExcept(?array $except = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterGroup(?\N1ebieski\IDir\Models\Group $group = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterOrderBy(?string $orderby = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterRegion(?\N1ebieski\IDir\Models\Region\Region $region = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterReport(?int $report = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterSearch(?string $search = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterStatus(?int $status = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterType(?string $type = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price filterVisible(?int $visible = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Price query()
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereDiscountPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Price whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Price extends Model
{
    use HasFilterable;
    use HasCarbonable;
    use HasFactory;
    use HasFullTextSearchable;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'price',
        'discount_price',
        'days',
        'code',
        'token',
        'number'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    protected $searchable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = ['token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'type' => \N1ebieski\IDir\Casts\Price\TypeCast::class,
        'group_id' => 'integer',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'days' => 'integer',
        'number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'discount_price' => null
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return PriceFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Price\PriceFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\IDir\Models\Group::class);
    }

    /**
     * Undocumented function
     *
     * @return HasMany
     */
    public function codes(): HasMany
    {
        return $this->hasMany(\N1ebieski\IDir\Models\Code::class);
    }

    // Attributes

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function price(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Price\Price::class, [
            'price' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function regularPrice(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Price\RegularPrice::class, [
            'price' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function discountPrice(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Price\DiscountPrice::class, [
            'price' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function discount(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Price\Discount::class, [
            'price' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function codesAsString(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Price\CodesAsString::class, [
            'price' => $this
        ])();
    }

    /**
     *
     * @return Attribute
     * @throws BindingResolutionException
     */
    public function qrAsImage(): Attribute
    {
        return App::make(\N1ebieski\IDir\Attributes\Price\QrAsImage::class, [
            'price' => $this
        ])();
    }

    // Checkers

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isUnlimited(): bool
    {
        return $this->days === null;
    }

    // Factories

    /**
     * [makeService description]
     * @return PriceService [description]
     */
    public function makeService()
    {
        return App::make(PriceService::class, ['price' => $this]);
    }

    /**
     * [makeRepo description]
     * @return PriceRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PriceRepo::class, ['price' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return PriceFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }

    /**
     * [makeResource description]
     * @return PriceResource [description]
     */
    public function makeResource()
    {
        return App::make(PriceResource::class, ['price' => $this]);
    }
}
