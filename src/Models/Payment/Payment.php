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

namespace N1ebieski\IDir\Models\Payment;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\ValueObjects\Payment\Status;
use N1ebieski\ICore\Models\Traits\HasCarbonable;
use N1ebieski\ICore\Models\Traits\HasPolymorphic;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use N1ebieski\IDir\Services\Payment\PaymentService;
use N1ebieski\IDir\Repositories\Payment\PaymentRepo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\ICore\Models\Traits\HasFullTextSearchable;
use N1ebieski\IDir\Database\Factories\Payment\PaymentFactory;

/**
 * N1ebieski\IDir\Models\Payment\Payment
 *
 * @property Status $status
 * @property string $uuid
 * @property int|null $model_id
 * @property string|null $model_type
 * @property int $order_id
 * @property string $order_type
 * @property string|null $logs
 * @property string $driver
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $logs_as_html
 * @property-read string $poli
 * @property-read string $updated_at_diff
 * @property-read Model|\Eloquent $morph
 * @property-read Model|\Eloquent $orderMorph
 * @property-read \N1ebieski\ICore\Models\User|null $user
 * @method static \N1ebieski\IDir\Database\Factories\Payment\PaymentFactory factory(...$parameters)
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment orderBySearch(string $term)
 * @method static Builder|Payment pending()
 * @method static Builder|Payment poli()
 * @method static Builder|Payment poliType()
 * @method static Builder|Payment query()
 * @method static Builder|Payment search(string $term)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereDriver($value)
 * @method static Builder|Payment whereLogs($value)
 * @method static Builder|Payment whereModelId($value)
 * @method static Builder|Payment whereModelType($value)
 * @method static Builder|Payment whereOrderId($value)
 * @method static Builder|Payment whereOrderType($value)
 * @method static Builder|Payment whereStatus($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @method static Builder|Payment whereUuid($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasPolymorphic;
    use HasCarbonable;
    use HasFullTextSearchable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'status',
        'logs'
    ];

    /**
     * The columns of the full text index
     *
     * @var array
     */
    public $searchable = [
        'logs'
    ];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => Status::PENDING
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'model_id' => 'integer',
        'order_id' => 'integer',
        'status' => \N1ebieski\IDir\Casts\Payment\StatusCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return PaymentFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Payment\PaymentFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\N1ebieski\ICore\Models\User::class);
    }

    /**
     * Undocumented function
     *
     * @return MorphTo
     */
    public function morph(): MorphTo
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    /**
     * Undocumented function
     *
     * @return MorphTo
     */
    public function orderMorph(): MorphTo
    {
        return $this->morphTo('order', 'order_type', 'order_id');
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLogsAsHtmlAttribute(): string
    {
        return nl2br(e($this->logs));
    }

    // Scopes

    /**
     * [scopePublic description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', Status::PENDING);
    }

    // Factories

    /**
     * [makeService description]
     * @return PaymentService [description]
     */
    public function makeService()
    {
        return App::make(PaymentService::class, ['payment' => $this]);
    }

    /**
     * [makeRepo description]
     * @return PaymentRepo [description]
     */
    public function makeRepo()
    {
        return App::make(PaymentRepo::class, ['payment' => $this]);
    }

    /**
     * Undocumented function
     *
     * @param mixed $parameters
     * @return PaymentFactory
     */
    public static function makeFactory(...$parameters)
    {
        return static::factory($parameters);
    }
}
