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

use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\IDir\ValueObjects\Field\Required;

return [
    'group' => [
        'group' => 'Groups'
    ],
    'success' => [
        'store' => 'The field has been added.',
        'update' => 'The field has been updated.',
        'destroy' => 'The field was successfully deleted.'
    ],
    'error' => [
        'gus' => 'No company was found in the GUS database.'
    ],
    'route' => [
        'index' => 'Form fields',
        'edit' => 'Edit field',
        'create' => 'Add a field',
        'edit_position' => 'Edit position'
    ],
    'title' => 'Title',
    'desc' => 'Description',
    'choose' => 'Select from the list',
    'choose_type' => 'Select the field type',
    'min' => [
        'label' => 'Minimum number of characters',
    ],
    'max' => [
        'label' => 'Maximum number of characters',
    ],
    'options' => [
        'label' => 'Options',
        'tooltip' => 'Enter options from new line',
    ],
    'width' => [
        'label' => 'Maximum image width',
    ],
    'height' => [
        'label' => 'Maximum image height',
    ],
    'size' => [
        'label' => 'Maximum file size',
    ],
    'visible' => [
        'label' => 'Visibility',
        'tooltip' => 'Public - visible to everyone. Private - visible for moderators.',
        Visible::INACTIVE => 'private',
        Visible::ACTIVE => 'public'
    ],
    'required' => [
        'label' => 'Field condition',
        Required::INACTIVE => 'optional',
        Required::ACTIVE => 'required',
    ],
    'groups' => 'Applies to groups',
    'remove_marker' => 'Remove marker',
    'add_marker' => 'Add marker',
    'gus' => [
        'placeholder' => 'Enter number'
    ]
];
