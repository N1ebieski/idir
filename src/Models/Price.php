<?php

namespace N1ebieski\IDir\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Repositories\Price\PriceRepo;
use N1ebieski\IDir\ValueObjects\Price\Type;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Models\Traits\HasFilterable;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Services\Price\PriceService;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Database\Factories\Price\PriceFactory;

/**
 * @property Type $type
 */
class Price extends Model
{
    use HasFilterable;
    use HasCarbonable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
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
     * @var array
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

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getPriceAttribute(): string
    {
        return is_null($this->discount_price) ? $this->regular_price : $this->discount_price;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getRegularPriceAttribute(): string
    {
        return $this->attributes['price'];
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getDiscountPriceAttribute(): ?string
    {
        return Config::get('idir.price.discount') === true ?
            $this->attributes['discount_price']
            : null;
    }

    /**
     * Undocumented function
     *
     * @return integer|null
     */
    public function getDiscountAttribute(): ?int
    {
        if (is_numeric($this->discount_price) && $this->discount_price < $this->regular_price) {
            return (int)round(
                ($this->regular_price - $this->discount_price) / $this->regular_price * 100,
                0,
                PHP_ROUND_HALF_DOWN
            );
        }

        return null;
    }

    /**
     * [getCodesAsStringAttribute description]
     * @return string|null [description]
     */
    public function getCodesAsStringAttribute(): ?string
    {
        if ($this->codes instanceof Collection && $this->codes->isNotEmpty()) {
            foreach ($this->codes as $code) {
                $codes[] = $code->code . '|' . ($code->quantity !== null ? $code->quantity : 0);
            }

            return (string)implode("\r\n", $codes);
        }

        return $this->attributes['codes']['codes'] ?? null;
    }

    /**
     * [getCodesAttribute description]
     * @return Collect|null [description]
     */
    public function getCodesAttribute(): Collect
    {
        if ($this->relationLoaded('codes') && $this->getRelation('codes') instanceof Collection) {
            return $this->getRelation('codes');
        }

        return isset($this->attributes['codes']['codes']) ?
            Collect::make(explode("\r\n", $this->attributes['codes']['codes']))
            : Collect::make([]);
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    public function getQrAsImageAttribute(): ?string
    {
        return ($this->type->isCodeSms()) ?
            QrCode::encoding('UTF-8')->generate("smsto:{$this->number}:{$this->code}")
            : null;
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
}
