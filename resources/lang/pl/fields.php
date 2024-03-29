<?php

use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\IDir\ValueObjects\Field\Required;

return [
    'group' => [
        'group' => 'Grupy'
    ],
    'success' => [
        'store' => 'Pole zostało dodane.',
        'update' => 'Pole zostało zaktualizowane.',
        'destroy' => 'Pomyślnie usunięto pole.'
    ],
    'error' => [
        'gus' => 'Nie znaleziono żadnej firmy w bazie GUS.'
    ],
    'route' => [
        'index' => 'Pola formularza',
        'edit' => 'Edycja pola',
        'create' => 'Dodaj pole',
        'edit_position' => 'Edycja pozycji'
    ],
    'title' => 'Tytuł pola',
    'desc' => 'Opis pola',
    'choose' => 'Wybierz z listy',
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
        Visible::INACTIVE => 'prywatna',
        Visible::ACTIVE => 'publiczna'
    ],
    'required' => [
        'label' => 'Warunek pola',
        Required::INACTIVE => 'nieobowiązkowe',
        Required::ACTIVE => 'obowiązkowe',
    ],
    'groups' => 'Dotyczy grup',
    'remove_marker' => 'Usuń marker',
    'add_marker' => 'Dodaj marker',
    'gus' => [
        'placeholder' => 'Wpisz numer'
    ]
];
