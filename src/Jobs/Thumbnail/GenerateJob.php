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
use Illuminate\Contracts\Filesystem\Factory as Storage;

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
        LocalThumbnail $thumbnail,
        Filesystem $filesystem,
        Storage $storage
    ): void {
        $storage->disk($this->disk)->makeDirectory(
            Str::beforeLast($thumbnail->getFilePath(), '/')
        );

        try {
            $browsershot->url($this->url)
                ->windowSize(1366, 1024)
                ->setDelay(5000)
                ->fit(\Spatie\Image\Manipulations::FIT_CONTAIN, 400, 300)
                ->save($storage->disk($this->disk)->path($thumbnail->getFilePath()));
        } catch (\Exception $e) {
            $contents = $filesystem->get(public_path() . '/images/vendor/idir/thumbnail-default.png');

            $storage->disk($this->disk)->put($thumbnail->getFilePath(), $contents);

            throw $e;
        }
    }
}
