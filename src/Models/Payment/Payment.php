<?php

namespace N1ebieski\IDir\Models\Payment;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\Repositories\PaymentRepo;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use N1ebieski\IDir\ValueObjects\Payment\Status;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use N1ebieski\IDir\Services\Payment\PaymentService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use N1ebieski\IDir\Database\Factories\Payment\PaymentFactory;

/**
 * @property Status $status
 */
class Payment extends Model
{
    use Polymorphic;
    use Carbonable;
    use FullTextSearchable;
    use HasFactory;

    // Configuration

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
     * @var array
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
