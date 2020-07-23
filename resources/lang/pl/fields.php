<?php

use N1ebieski\IDir\Models\Field\Field;

return [
    'group' => [
        'group' => 'Grupy'
    ],
    'success' => [
        'store' => 'Pole zostało dodane.',
        'update' => 'Pole zostało zaktualizowane.',
        'destroy' => 'Pomyślnie usunięto pole.'
    ],
    'route' => [
        'index' => 'Pola formularza',
        'edit' => 'Edycja pola',
        'create' => 'Dodaj pole',
        'edit_position' => 'Edycja pozycji'
    ],
    'title' => 'Tytuł pola',
    'desc' => 'Opis pola',
    'choose_type' => 'Wybierz typ pola',
    'min' => [
        'label' => 'Minimalna ilość znaków',
    ],
    'max' => [
        'label' => 'Maksymalna ilość znaków',
    ],
    'options' => [
        'label' => 'Opcje',
        'tooltip' => 'Opcje wpisuj od nowej linii',
    ],
    'width' => [
        'label' => 'Maksymalna szerokość obrazu',
    ],
    'height' => [
        'label' => 'Maksymalna wysokość obrazu',
    ],
    'size' => [
        'label' => 'Maksymalna wielkość pliku',
    ],
    'visible' => [
        'label' => 'Widoczność',
        'tooltip' => 'Publiczna - widoczna dla wszystkich. Prywatna - widoczna dla ról z uprawnieniem admina.',
        Field::INVISIBLE => 'prywatna',
        Field::VISIBLE => 'publiczna'
    ],
    'required' => [
        'label' => 'Warunek pola',
        Field::OPTIONAL => 'nieobowiązkowe',
        Field::REQUIRED => 'obowiązkowe',
    ],
    'groups' => 'Dotyczy grup',
    'remove_marker' => 'Usuń marker',
    'add_marker' => 'Dodaj marker'
];
