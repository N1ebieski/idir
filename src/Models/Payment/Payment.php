<?php

namespace N1ebieski\IDir\Models\Payment;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\IDir\Repositories\PaymentRepo;
use N1ebieski\ICore\Models\Traits\Carbonable;
use N1ebieski\ICore\Models\Traits\Polymorphic;
use N1ebieski\IDir\Services\Payment\PaymentService;
use N1ebieski\ICore\Models\Traits\FullTextSearchable;

class Payment extends Model
{
    use Polymorphic, Carbonable, FullTextSearchable;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'model_id' => 'integer',
        'order_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

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
     * [orderMorph description]
     * @return [type] [description]
     */
    public function orderMorph()
    {
        return $this->morphTo('order', 'order_type', 'order_id');
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
}
