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

namespace N1ebieski\IDir\Jobs\Thumbnail;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Spatie\Browsershot\Browsershot;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use N1ebieski\IDir\Utils\Thumbnail\LocalThumbnail;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\IDir\Utils\Thumbnail\Interfaces\ThumbnailInterface;

class GenerateJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    public function __construct(
        public string $url,
        public string $disk,
    ) {
        $this->onQueue('thumbnail');
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->url;
    }

    public function handle(
        Browsershot $browsershot,
        ThumbnailInterface $thumbnail,
        Filesystem $filesystem,
        Storage $storage,
        Config $config
    ): void {
        $storage->disk($this->disk)->makeDirectory(
            Str::beforeLast($thumbnail->getFilePath(), '/')
        );

        try {
            $browsershot = $browsershot->url($this->url)
                ->windowSize(
                    $config->get('idir.dir.thumbnail.local.window_size.width'),
                    $config->get('idir.dir.thumbnail.local.window_size.height')
                )
                ->dismissDialogs()
                ->ignoreHttpsErrors()
                ->setEnvironmentOptions([
                    'LANG' => $config->get('app.locale'),
                ])
                ->setDelay($config->get('idir.dir.thumbnail.local.delay') * 1000)
                ->userAgent(Arr::random($config->get('idir.puppeteer.user_agents')))
                ->fit(
                    \Spatie\Image\Manipulations::FIT_CONTAIN,
                    $config->get('idir.dir.thumbnail.local.image_size.width'),
                    $config->get('idir.dir.thumbnail.local.image_size.height')
                )
                ->setScreenshotType('jpeg', 80);

            if ($config->get('idir.puppeteer.node_path')) {
                $browsershot = $browsershot->setNodeBinary($config->get('idir.puppeteer.node_path'));
            }

            if ($config->get('idir.puppeteer.node_module_path')) {
                $browsershot = $browsershot->setNodeModulePath($config->get('idir.puppeteer.node_module_path'));
            }

            if ($config->get('idir.puppeteer.npm_path')) {
                $browsershot = $browsershot->setNpmBinary($config->get('idir.puppeteer.npm_path'));
            }

            if ($config->get('idir.puppeteer.chrome_path')) {
                $browsershot = $browsershot->setChromePath($config->get('idir.puppeteer.chrome_path'));
            }

            if ($config->get('idir.puppeteer.proxy_servers')) {
                $browsershot = $browsershot->setProxyServer($config->get('idir.puppeteer.proxy_servers'));
            }

            /** @disregard */
            /** @var string $path */
            $path = $storage->disk($this->disk)->path($thumbnail->getFilePath());

            $browsershot->save($path);
        } catch (\Exception $e) {
            $contents = $filesystem->get(public_path() . '/images/vendor/idir/thumbnail-default.png');

            $storage->disk($this->disk)->put($thumbnail->getFilePath(), $contents);

            throw $e;
        }
    }
}
