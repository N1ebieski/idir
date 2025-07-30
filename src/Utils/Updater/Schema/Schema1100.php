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

namespace N1ebieski\IDir\Utils\Updater\Schema;

use N1ebieski\ICore\Utils\Updater\Schema\Interfaces\SchemaInterface;

class Schema1100 implements SchemaInterface
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pattern = [
        [
            'paths' => [
                'resources/views/vendor/idir/web/dir/create/2.blade.php',
            ],
            'actions' => [
                [
                    'type' => [
                        'move',
                        'afterFirst'
                    ],
                    'search' => '/\@if\s*\(\s*\!\$group->url->isInactive\(\)\s*\)[\s\S]*?<div[\s\S]*?\/div>[\s\S]*?\@endif/',
                    'to' => '/<div\s*class="form-group">[\s\S]*?<label\s*for="title"[\s\S]*?\[\'name\'\s*=>\s*\'title\'\]\)[\s\S]*?<\/div>/'
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/<label\s*for="url">[\s\S]*?<\/label>/',
                    'to' => <<<'BLADE'
                    @if ($group->hasGenerateContentPrivilege())
                    <div 
                        data-route="{{ route('web.dir.generate_content', [$group->id]) }}"
                        class="search position-relative"
                        id="generate-content" 
                    >
                        <div class="input-group">
                    @endif                    
BLADE
                ],
                [
                    'type' => 'beforeFirst',
                    'search' => '/\@includeWhen\(\$errors->has\(\'url\'\)/',
                    'to' => <<<'BLADE'
                    @if ($group->hasGenerateContentPrivilege())
                            <span class="input-group-append">
                                <button 
                                    class="btn btn-outline-secondary border border-left-0"
                                    type="button"
                                >
                                    <i class="fas fa-pencil-alt"></i>
                                    <span class="d-none d-md-inline">{{ trans('idir::dirs.generate_content') }}</span>
                                </button>
                            </span> 
                        </div>
                    </div>
                    @endif
BLADE
                ]
            ]
        ],
        [
            'paths' => [
                'resources/lang/pl.json'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/"additional link on the friends subpage":/',
                    'to' => '"generate content by AI": "generowanie treści przez AI",'
                ],
                [
                    'type' => 'beforeFirst',
                    'search' => '/"generate content by AI":/',
                    'to' => '"Too many attempts. You may try again in :minutes minutes.": "Przekroczyłeś limit. Sprobuj ponownie za :minutes minut.",'
                ],
            ]
        ],
        [
            'paths' => [
                'routes/vendor/idir/web/dirs.php',
            ],
            'actions' => [
                [
                    'type' => 'afterFirst',
                    'search' => '/use Illuminate\\\Support\\\Facades\\\Route;/',
                    'to' => "use Illuminate\Support\Facades\Config;"
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/->name\(\'dir\.show\'\)[\s\S]*?->where\(\'dir_cache\', \'\[0-9A-Za-z,_-\]\+\'\);/',
                    'to' => <<<'PHP'
Route::post('dirs/group/{group}/generate-content', [DirController::class, 'generateContent'])
    ->where('group', '[0-9]+')
    ->name('dir.generate_content')
    ->middleware('icore.rate.limiter.per.hour:' . Config::get('idir.dir.generate_content.max_attempts'));
PHP
                ]
            ]
        ],
        [
            'paths' => [
                'app/Console/Kernel.php',
            ],
            'actions' => [
                [
                    'type' => 'afterFirst',
                    'search' => '/use Illuminate\\\Console\\\Scheduling\\\Schedule;/',
                    'to' => <<<'PHP'
use Illuminate\Support\Facades\Config;
use N1ebieski\IDir\ValueObjects\Thumbnail\Driver;
PHP
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/protected function schedule\(Schedule \$schedule\)[\s\S]*?{/',
                    'to' => <<<'PHP'
        /** @var Driver $driver */
        $driver = Config::get('idir.dir.thumbnail.driver');

        if ($driver->isEquals(Driver::Local)) {
            $schedule->command('queue:work --queue=thumbnail --daemon --stop-when-empty --tries=3')->withoutOverlapping();
        }
PHP
                ]
            ]
        ],
        [
            'paths' => [
                'config/purifier.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/\'dir\'\s*=>\s*\[/',
                    'to' => <<<'PHP'
        'html' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'title,meta[name|content],div,b,strong,i,em,u,a[href|title|target|rel],ul,ol,li,p,br,span,img[alt|src],sub,sup,hr,h1,h2,h3,h4,h5,blockquote,del,table,thead,tbody,th,tr,td',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => true,
            'Core.ConvertDocumentToFragment' => false
        ],
PHP
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/\'elements\'\s*=>\s*\[/',
                    'to' => <<<'PHP'
                ['title', 'Inline', 'Inline', 'Common'],
                ['meta', 'Inline', 'Empty', 'Common', [
                    'name'    => 'Text',
                    'content' => 'Text',
                ]],
PHP
                ],
                [
                    'type' => 'afterFirst',
                    'search' => '/\'custom_attributes\'\s*=>\s*\[/',
                    'to' => <<<'PHP'
            ['meta', 'name', 'Enum#keywords,description', 'start'],
PHP
                ]
            ]
        ],
        [
            'paths' => [
                'config/services.php'
            ],
            'actions' => [
                [
                    'type' => 'beforeFirst',
                    'search' => '/\'gus\'\s*=>\s*\[/',
                    'to' => <<<'PHP'
    'openai' => [
        'api_key' => env('OPENAI_API_KEY')
    ],
PHP
                ]
            ]
        ]
    ];
}
