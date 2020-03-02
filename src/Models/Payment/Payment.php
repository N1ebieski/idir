<?php

namespace N1ebieski\IDir\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Services\PaymentService;
use N1ebieski\IDir\Repositories\PaymentRepo;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use Ramsey\Uuid\Uuid;

/**
 * [Payment description]
 */
class Payment extends Model
{
    use Polymorphic, Carbonable;

    // Configuration

    /**
     * [public description]
     * @var int
     */
    public const FINISHED = 1;

    /**
     * [public description]
     * @var int
     */
    public const UNFINISHED = 0;

    /**
     * [public description]
     * @var int
     */
    public const PENDING = 2;

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

    // Relations

    /**
     * [user description]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('N1ebieski\ICore\Models\User');
    }

    /**
     * [morph description]
     * @return [type] [description]
     */
    public function morph()
    {
        return $this->morphTo('morph', 'model_type', 'model_id');
    }

    /**
     * [priceMorph description]
     * @return [type] [description]
     */
    public function price_morph()
    {
        return $this->morphTo('price', 'price_type', 'price_id');
    }

    // Overrides

    /**
     * Undocumented function
     *
     * @return void
     */
    public static function boot() : void
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = (string)Uuid::uuid4();
        });
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getLogsAsHtmlAttribute() : string
    {
        return nl2br(e($this->logs));
    }

    // Scopes

    /**
     * [scopePublic description]
     * @param  Builder $query [description]
     * @return Builder        [description]
     */
    public function scopePending(Builder $query) : Builder
    {
        return $query->where('status', static::PENDING);
    }

    // Checkers

    /**
     * [isPending description]
     * @return bool [description]
     */
    public function isPending() : bool
    {
        return $this->status === static::PENDING;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isUnfinished() : bool
    {
        return $this->status === static::UNFINISHED;
    }

    // Makers

    /**
     * [makeService description]
     * @return PaymentService [description]
     */
    public function makeService()
    {
        return app()->make(PaymentService::class, ['payment' => $this]);
    }

    /**
     * [makeRepo description]
     * @return PaymentRepo [description]
     */
    public function makeRepo()
    {
        return app()->make(PaymentRepo::class, ['payment' => $this]);
    }
}
