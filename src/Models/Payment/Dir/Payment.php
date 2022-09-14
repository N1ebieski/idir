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

namespace N1ebieski\IDir\Models\Payment\Dir;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Price;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use N1ebieski\IDir\Models\Payment\Payment as BasePayment;
use N1ebieski\IDir\Database\Factories\Payment\Dir\PaymentFactory;

/**
 * N1ebieski\IDir\Models\Payment\Dir\Payment
 *
 * @property Dir $morph
 * @property Price $order
 * @property string $uuid
 * @property int|null $model_id
 * @property string $model_type
 * @property int $order_id
 * @property string $order_type
 * @property \N1ebieski\IDir\ValueObjects\Payment\Status $status
 * @property string|null $logs
 * @property string $driver
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_diff
 * @property-read string $logs_as_html
 * @property-read string $order_model_type
 * @property-read string $poli
 * @property-read string $updated_at_diff
 * @property-read \N1ebieski\IDir\Models\Group|null $group
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $orderMorph
 * @property-read \N1ebieski\ICore\Models\User|null $user
 * @method static \N1ebieski\IDir\Database\Factories\Payment\Dir\PaymentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static Builder|Payment orderBySearch(string $term)
 * @method static Builder|Payment pending()
 * @method static Builder|Payment poli()
 * @method static Builder|Payment poliType()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static Builder|Payment search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDriver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereLogs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUuid($value)
 * @mixin \Eloquent
 */
class Payment extends BasePayment
{
    // Configurations

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\IDir\Models\Payment\Payment::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return PaymentFactory
     */
    protected static function newFactory()
    {
        return \N1ebieski\IDir\Database\Factories\Payment\Dir\PaymentFactory::new();
    }

    // Relations

    /**
     * Undocumented function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function group(): HasOneThrough
    {
        return $this->hasOneThrough(
            \N1ebieski\IDir\Models\Group::class,
            \N1ebieski\IDir\Models\Price::class,
            'id',
            'id',
            'order_id',
            'group_id'
        );
    }

    // Accessors

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getModelTypeAttribute(): string
    {
        return \N1ebieski\IDir\Models\Dir::class;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getOrderModelTypeAttribute(): string
    {
        return \N1ebieski\IDir\Models\Price::class;
    }

    /**
     * [getPoliAttribute description]
     * @return string [description]
     */
    public function getPoliAttribute(): string
    {
        return 'dir';
    }

    // Factories

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
