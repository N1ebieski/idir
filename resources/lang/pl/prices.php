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

use N1ebieski\IDir\ValueObjects\Price\Type;

return [
    'success' => [
        'store' => 'Cena została dodana.',
        'update' => 'Cena została zaktualizowana.',
        'destroy' => 'Pomyślnie usunięto cenę.'
    ],
    'route' => [
        'index' => 'Ceny',
        'edit' => 'Edycja ceny',
        'create' => 'Dodaj cenę',
        'show' => 'Cena: :price'
    ],
    'days' => 'Dni',
    'price' => 'Cena',
    'discount_price' => [
        'label' => 'Cena ze zniżką',
        'tooltip' => 'W przypadku braku zniżki, pozostawić puste',
    ],
    'price_from' => 'od :price :currency <small class="text-muted">/ :days :limit</small>',
    'unlimited' => 'nielimitowany',
    'group' => 'Grupa',
    'code_sms' => 'Kod SMS',
    'code_transfer' => 'Kod ID',
    'token' => 'Token',
    'number' => 'Numer',
    'codes' => 'Kody manualne',
    'sync_codes' => 'Synchronizuj kody',
    'payment' => [
        'label' => 'Metoda płatności',
        Type::TRANSFER => 'Przelew online',
        Type::CODE_SMS => 'Kody SMS',
        Type::CODE_TRANSFER => 'Kody przelewem',
        Type::PAYPAL_EXPRESS => 'PayPal'
    ]
];
