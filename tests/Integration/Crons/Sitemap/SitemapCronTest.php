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

namespace N1ebieski\IDir\Tests\Integration\Crons\Sitemap;

use Closure;
use XMLReader;
use Tests\TestCase;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use N1ebieski\IDir\Crons\Sitemap\SitemapCron;
use N1ebieski\IDir\Models\Category\Dir\Category;
use N1ebieski\ICore\Crons\Sitemap\Builder\Builder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Contracts\Container\BindingResolutionException;

class SitemapCronTest extends TestCase
{
    use DatabaseTransactions;

    /**
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('icore.sitemap.limit', 5);
        Config::set('icore.sitemap.max_items', 50);

        File::deleteDirectory(App::storagePath('app/public/vendor/idir/sitemap'));
    }

    /**
     *
     * @return array
     */
    protected function sitemapCronProvider(): array
    {
        return [
            [
                \N1ebieski\IDir\Crons\Sitemap\Builder\Category\Dir\CategoryBuilder::class,
                function () {
                    return Category::makeFactory()->active()->count(100)->create();
                },
                function (Builder $builder, Category $category) {
                    return URL::route($builder->route, [$category->slug]);
                }
            ],
            [
                \N1ebieski\IDir\Crons\Sitemap\Builder\DirBuilder::class,
                function () {
                    return Dir::makeFactory()->active()->withDefaultGroup()->count(100)->create();
                },
                function (Builder $builder, Dir $dir) {
                    return URL::route($builder->route, [$dir->slug]);
                }
            ]
        ];
    }

    /**
     * @dataProvider sitemapCronProvider
     */
    public function testCron(string $namespace, Closure $seed, Closure $route): void
    {
        /** @var mixed */
        $modelBuilder = App::make($namespace);

        /** @var Collection */
        $models = $seed();

        $schedule = App::make(SitemapCron::class);
        $schedule();

        $modelSitemapPath = $modelBuilder->path . '/sitemap-2.xml';

        $this->assertFileExists(App::storagePath('app/public/' . $modelSitemapPath));

        $xml = new \XMLReader();

        $xml->open(App::storagePath('app/public/' . $modelSitemapPath));

        $xml->setParserProperty(XMLReader::VALIDATE, true);

        $this->assertTrue($xml->isValid());

        /** @var string */
        $modelSitemapContents = Storage::disk('public')->get($modelSitemapPath);

        $this->assertStringContainsString($route($modelBuilder, $models[50]), $modelSitemapContents);

        /** @var \N1ebieski\IDir\Crons\Sitemap\Builder\SitemapBuilder */
        $sitemapBuilder = App::make(\N1ebieski\IDir\Crons\Sitemap\Builder\SitemapBuilder::class);

        $sitemapPath = $sitemapBuilder->path . '/sitemap.xml';

        $this->assertFileExists(App::storagePath('app/public/' . $sitemapPath));

        $xml->open(App::storagePath('app/public/' . $sitemapPath));

        $xml->setParserProperty(XMLReader::VALIDATE, true);

        $this->assertTrue($xml->isValid());

        /** @var string */
        $sitemapContents = Storage::disk('public')->get($sitemapPath);

        $this->assertStringContainsString(URL::to('storage/' . $modelSitemapPath), $sitemapContents);
    }
}
