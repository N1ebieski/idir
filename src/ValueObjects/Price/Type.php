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

namespace N1ebieski\IDir\ValueObjects\Price;

use N1ebieski\ICore\ValueObjects\ValueObject;

class Type extends ValueObject
{
    /**
     * [public description]
     * @var string
     */
    public const TRANSFER = 'transfer';

    /**
     * [public description]
     * @var string
     */
    public const CODE_TRANSFER = 'code_transfer';

    /**
     * [public description]
     * @var string
     */
    public const CODE_SMS = 'code_sms';

    /**
     * [public description]
     * @var string
     */
    public const PAYPAL_EXPRESS = 'paypal_express';

    /**
     * Undocumented function
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->validate($value);

        $this->value = $value;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return void
     */
    protected function validate(string $value): void
    {
        $in = [self::TRANSFER, self::CODE_TRANSFER, self::CODE_SMS, self::PAYPAL_EXPRESS];

        if (!in_array($value, $in)) {
            throw new \InvalidArgumentException("The given value must be in: " . implode(', ', $in));
        }
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function getAvailable(): array
    {
        return [self::TRANSFER, self::CODE_TRANSFER, self::CODE_SMS, self::PAYPAL_EXPRESS];
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isTransfer(): bool
    {
        return $this->value === self::TRANSFER;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isCode(): bool
    {
        return in_array($this->value, [self::CODE_TRANSFER, self::CODE_SMS]);
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isCodeTransfer(): bool
    {
        return $this->value === self::CODE_TRANSFER;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isCodeSms(): bool
    {
        return $this->value === self::CODE_SMS;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function isPaypalExpress(): bool
    {
        return $this->value === self::PAYPAL_EXPRESS;
    }
}
