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

use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Price\Type;

return [
    'route' => [
        'index' => 'Katalog',
        'search' => 'Wyszukiwanie: :search',
        'create' => [
            'index' => 'Dodaj wpis',
            '1' => 'Wybierz typ wpisu',
            '2' => 'Wypełnij formularz',
            '3' => 'Podsumowanie'
        ],
        'edit' => [
            'index' => 'Edytuj wpis',
            'renew' => 'Przedłuż ważność',
            '1' => 'Wybierz typ wpisu',
            '2' => 'Wypełnij formularz',
            '3' => 'Podsumowanie'
        ],
        'step' => 'Krok :step'
    ],
    'success' => [
        'store' => [
            Status::INACTIVE => 'Wpis został dodany i oczekuje na akceptację przez moderatora.',
            Status::ACTIVE => 'Wpis został dodany i jest aktywny.'
        ],
        'update' => [
            Status::INACTIVE => 'Wpis został zaktualizowany i oczekuje na akceptację przez moderatora.',
            Status::ACTIVE => 'Wpis został zaktualizowany i jest aktywny.'
        ],
        'update_status' => [
            Status::ACTIVE => 'Wpis został aktywowany',
            Status::INCORRECT_INACTIVE => 'Wpis został zgłoszony do poprawy'
        ],
        'update_renew' => [
            Status::INACTIVE => 'Dziękujemy. Czas ważności wpisu zostanie przedłużony w momencie akceptacji wpisu przez moderację.',
            Status::ACTIVE => 'Dziękujemy. Czas ważności wpisu został przedłużony.'
        ],
        'destroy' => 'Wpis został usunięty',
        'destroy_global' => 'Pomyślnie usunięto :affected wpisów'
    ],
    'choose_group' => 'Wybierz grupę',
    'change_group' => 'Zmień grupę',
    'renew_group' => 'Przedłuż ważność',
    'categories' => 'Kategorie',
    'tags' => [
        'label' => 'Tagi',
        'tooltip' => 'Min 3 znaki, max :max_chars znaków, max :max_tags tagów',
        'placeholder' => 'Dodaj tag'
    ],
    'choose_payment_type' => 'Wybierz typ płatności',
    'payment' => [
        Type::TRANSFER => [
            'label' => 'Przelew online',
            'info' => 'Płatności internetowe przelewem realizuje <a href=":provider_url" target="_blank" rel="noopener" title=":provider_name">:provider_name</a>. Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name dokumenty">:provider_name dokumenty</a>. Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name regulamin">:provider_name regulamin</a>. Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank" rel="noopener" title="Regulamin">regulaminu</a>.',
        ],
        Type::CODE_SMS => [
            'label' => 'Kod automatyczny SMS',
            'info' => 'Aby otrzymać kod dostępu - wyślij SMS o treści <b><span id="code_sms">:code_sms</span></b> na numer <b><span id="number">:number</span></b>. Koszt SMSa to <b><span id="price">:price</span></b> PLN. Usługa SMS dostępna jest dla wszystkich operatorów sieci komórkowych w Polsce. Płatności SMS w serwisie obsługuje <a href=":provider_url" target="_blank" rel="noopener" title=":provider_name">:provider_name</a>. Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name dokumenty">:provider_name dokumenty</a>. Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name regulamin">:provider_name regulamin</a>. Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank" rel="noopener" title="Regulamin">regulaminu</a>.'
        ],
        Type::CODE_TRANSFER => [
            'label' => 'Kod automatyczny przelewem',
            'info' => 'Aby otrzymać kod dostępu - dokonaj płatności przelewem na stronie zakupu kodów <a id="code_transfer" href=":code_transfer_url" target="blank" title=":provider_name"><b>:provider_name</b></a>. Koszt to <b><span id="price">:price</span></b> PLN. Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name dokumenty">:provider_name dokumenty</a>. Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name regulamin">:provider_name regulamin</a>. Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank" rel="noopener" title="Regulamin">regulaminu</a>.'
        ],
        Type::PAYPAL_EXPRESS => [
            'label' => 'PayPal',
            'info' => 'Płatności internetowe realizuje <a href=":provider_url" target="_blank" rel="noopener" title=":provider_name">:provider_name</a>. Dokumenty dotyczące systemu płatności dostępne są na stronie <a href=":provider_docs_url" target="_blank" rel="noopener" title=":provider_name dokumenty">:provider_name dokumenty</a>. Regulamin usługi dostępny jest na stronie <a href=":provider_rules_url" target="_blank" rel="noopener" title=":provider_name regulamin">:provider_name regulamin</a>. Zgłoszenie strony do katalogu równoznaczne jest z akceptacją <a href=":rules_url" target="_blank" rel="noopener" title="Regulamin">regulaminu</a>.',
        ]
    ],
    'price' => ':price :currency / :days :limit',
    'rules' => 'Regulamin',
    'code' => 'Wpisz kod',
    'choose_backlink' => 'Wybierz link zwrotny',
    'backlink_url' => 'Adres z linkiem zwrotnym',
    'group' => 'Grupa',
    'group_limit' => 'Limit wyczerpany (max: :dirs, dzienny: :dirs_today)',
    'unlimited' => 'nielimitowany',
    'status' => [
        'label' => 'Status',
        Status::ACTIVE => 'aktywny',
        Status::INACTIVE => 'oczekujący na moderację',
        Status::PAYMENT_INACTIVE => 'oczekujący na płatność',
        Status::BACKLINK_INACTIVE => 'oczekujący na backlink',
        Status::STATUS_INACTIVE => 'oczekujący na status 200',
        Status::INCORRECT_INACTIVE => 'oczekujący na poprawę'
    ],
    'privileged_to' => 'Data wygaśnięcia',
    'reason' => [
        'label' => 'Powód odrzucenia',
        'custom' => 'Inny powód'
    ],
    'mail' => [
        'delete' => [
            'info' => 'Niestety przykro nam, ale Twój wpis :dir_link został usunięty z naszego katalogu.'
        ],
        'activation' => [
            'info' => 'Gratulujemy, Twój wpis :dir_link został poprawnie dodany do naszego katalogu i znajduje się na stronie: <a href=":dir_page">:dir_page</a>. Zapraszamy do kolejnych wpisów!'
        ],
        'incorrect' => [
            'info' => 'Niestety przykro nam, ale Twój wpis :dir_link nie jest zgodny z naszym regulaminem i wymaga poprawy treści. Do tego czasu pozostanie nieaktywny.',
            'edit_dir' => 'Edycji swojego wpisu dokonasz klikając w przycisk poniżej:'
        ],
        'reminder' => [
            'title' => 'Przypomnienie o wygasającym wpisie',
            'info' => 'Przypominamy, że kończy się czas ważności Twojego wpisu :dir_link znajdującego się na stronie: <a href=":dir_page">:dir_page</a> w grupie :group.',
            'alt' => 'Po upływie czasu ważności :days, wpis zostanie przeniesiony do niższej grupy :alt_group.',
            'deactivation' => 'Po upływie czasu ważności :days, wpis zostanie deaktywowany ze statusem "oczekujący na płatność".',
            'renew_dir' => 'Możesz temu zapobiec przedłużając czas ważności wpisu w aktualnej grupie. Przedłużenia swojego wpisu dokonasz klikając w przycisk poniżej:'
        ],
        'completed' => [
            'title' => 'Koniec czasu ważności',
            'info' => 'Informujemy, że zakończył się czas ważności Twojego wpisu :dir_link w grupie :group.',
            'alt' => 'Tym samym wpis został przeniesiony do niższej grupy :alt_group.',
            'deactivation' => 'Tym samym wpis został deaktywowany ze statusem "oczekujący na płatność".',
            'edit_dir' => 'Możesz odnowić wpis lub zmienić grupę. Edycji swojego wpisu dokonasz klikając w przycisk poniżej:'
        ]
    ],
    'link_dir_page' => 'Podlinkuj swój wpis by przyśpieszyć indeksację',
    'premium_dir' => 'Wyróżnij swój wpis na stronie',
    'content' => 'Opis',
    'author' => 'Autor',
    'reload_thumbnail' => 'Odśwież',
    'check_content' => 'Sprawdź unikalność opisu',
    'rating' => 'Ocena',
    'url' => 'Adres strony',
    'related' => 'Podobne wpisy',
    'latest' => 'Najnowsze wpisy',
    'title' => 'Tytuł',
    'email' => [
        'label' => 'Adres e-mail',
        'tooltip' => 'Na ten adres zostanie założone konto użytkownika. Jesli masz już konto - zaloguj się.'
    ],
    'notes' => 'Uwagi moderatora',
    'more' => 'pokaż więcej &raquo;',
    'correct' => 'Poprawa',
    'confirm' => [
        'correct' => 'Czy na pewno chcesz zgłosić wpis do poprawy?'
    ],
    'chart' => [
        'x' => [
            'label' => 'Data'
        ],
        'y' => [
            'label' => 'Ilość wpisów'
        ],
        'count_by_status' => 'Wykres ilości wpisów wg statusu',
        'count_by_group' => 'Wykres ilości wpisów wg grup',
        'count_by_date_and_group' => 'Wykres ilości wpisów wg grup na osi czasu'
    ],
    'error' => [
        'generate_content' => [
            'dir_status' => 'Nie udało się pobrać zawartości strony. Upewnij się, że nie blokujesz bota o numerze IP: :ip',
            'ai_empty' => 'Odpowiedź od providera jest pusta. Popraw tytuł wpisu i spróbuj ponownie.',
            'ai_invalid' => 'Odpowiedź od providera jest nieprawidłowa. Popraw tytuł wpisu i spróbuj ponownie.',
            'ai' => 'Nie udało się wygenerować treści dla wpisu. Być może provider jest przeciążony. Spróbuj ponownie później.'
        ]
    ],
    'generate_content' => 'Generuj treść'
];
