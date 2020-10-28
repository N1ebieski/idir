<?php

use N1ebieski\IDir\Models\Field\Field;

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
        Field::INVISIBLE => 'private',
        Field::VISIBLE => 'public'
    ],
    'required' => [
        'label' => 'Field condition',
        Field::OPTIONAL => 'optional',
        Field::REQUIRED => 'required',
    ],
    'groups' => 'Applies to groups',
    'remove_marker' => 'Remove marker',
    'add_marker' => 'Add marker',
    'gus' => [
        'placeholder' => 'Enter number'
    ]
];
