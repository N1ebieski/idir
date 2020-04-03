<?php

namespace N1ebieski\IDir\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * [AppServiceProvider description]
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $license = new License(
            $this->app->make(\GuzzleHttp\Client::class),
            $this->app['filesystem'],
            $this->app['hash.driver'],
            $this->app['config']
        );

        $license->authorize();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

use Illuminate\Contracts\Hashing\Hasher as Hash;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Http\Response as HttpResponse;
use GuzzleHttp\Client as GuzzleClient;

/**
 * [License description]
 */
final class License
{
    /**
     * Undocumented variable
     *
     * @var GuzzleClient
     */
    private $guzzle;

    /**
     * [private description]
     * @var Storage
     */
    private $storage;

    /**
     * [private description]
     * @var Hash
     */
    private $hash;

    /**
     * License key
     * @var string|null
     */
    private $licenseKey;

    /**
     * [private description]
     * @var int
     */
    private $secs;

    /**
     * [__construct description]
     * @param GuzzleClient $guzzle [description]
     * @param Storage $storage [description]
     * @param Hash    $hash    [description]
     * @param Config  $config  [description]
     */
    public function __construct(GuzzleClient $guzzle, Storage $storage, Hash $hash, Config $config)
    {
        $this->guzzle = $guzzle;
        $this->storage = $storage;
        $this->hash = $hash;

        $this->secs = 4*60*60;
        $this->licenseKey = $config->get('idir.license_key');
    }

    /**
     * [encode description]
     * @param  string $data [description]
     * @param  string $pwd  [description]
     * @return string       [description]
     */
    private static function encode(string $data, string $pwd) : string
    {
        $pwd_length = strlen($pwd);
        $x = $a = $j = 0;
        $Zcrypt = '';
        for ($i = 0; $i < 256; $i++) {
            $license[$i] = ord(substr($pwd, ($i % $pwd_length)+1, 1));
            $counter[$i] = $i;
        }
        for ($i = 0; $i < 256; $i++) {
            $x = ($x + $counter[$i] + $license[$i]) % 256;
            $temp_swap = $counter[$i];
            $counter[$i] = $counter[$x];
            $counter[$x] = $temp_swap;
        }
        for ($i = 0; $i < strlen($data); $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $counter[$a]) % 256;
            $temp = $counter[$a];
            $counter[$a] = $counter[$j];
            $counter[$j] = $temp;
            $k = $counter[(($counter[$a] + $counter[$j]) % 256)];
            $Zcipher = ord(substr($data, $i, 1)) ^ $k;
            $Zcrypt .= chr($Zcipher);
        }
        return $Zcrypt;
    }

    /**
     * [relative_path description]
     * @return string [description]
     */
    private static function makeRelativePath() : string
    {
        $path = explode('/', $_SERVER['SCRIPT_NAME']);
        array_pop($path);

        return is_array($path) ? implode('/', $path) : null;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    private static function makeHost() : string
    {
        $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $host = preg_replace('/:([0-9]+)/', '', $host);

        return $host;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    private static function makeDomain() : string
    {
        return self::makeHost().self::makeRelativePath();
    }

    /**
     * [isLicenseKey description]
     * @param string|null $licenseKey    [description]
     * @param string $domain [description]
     */
    public static function isLicenseKey($licenseKey, $domain) : void
    {
        if ($licenseKey !== bin2hex(self::encode($domain, 'mOMN3i26BCh8Xvuc'))) {
            throw new \N1ebieski\IDir\Exceptions\License\InvalidLicenseKeyException(
                'The license key is invalid or empty',
                HttpResponse::HTTP_FORBIDDEN
            );
        }
    }

    /**
     * [authorize description]
     */
    public function authorize() : void
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            return;
        }

        self::isLicenseKey($this->licenseKey, self::makeDomain());

        $timer = array_fill(0, 2, 0);
        if ($this->storage->disk('local')->exists('idir.license')) {
            $timer = explode('|', $this->storage->disk('local')->get('idir.license'));
        }

        $current = (int)(floor(time()/$this->secs));
        $delay = (int)(floor((time()-$timer[0])/$this->secs));

        foreach ([$current, $delay] as $time) {
            if ($this->hash->check($time, $timer[1])) {
                return;
            }
        }

        try {
            $response = $this->guzzle->request(
                'GET',
                'https://intelekt.net.pl.local:8443/api/licenses/' . $this->licenseKey,
                [
                    'verify' => false
                ]
            );
            $response = json_decode($response->getBody());
        } catch (\Exception $e) {
            //
        }

        if (isset($response->server_status) && $response->server_status === 'OK') {
            if (isset($response->status) && $response->status === 'Not found') {
                throw new \N1ebieski\IDir\Exceptions\License\NotFoundException(
                    'License not found',
                    HttpResponse::HTTP_FORBIDDEN
                );
            }
            if (isset($response->status) && $response->status !== 'Active') {
                throw new \N1ebieski\IDir\Exceptions\License\InvalidStatusException(
                    'License status is invalid',
                    HttpResponse::HTTP_FORBIDDEN
                );
            }
            if (isset($response->domain) && $response->domain !== self::makeDomain()) {
                throw new \N1ebieski\IDir\Exceptions\License\InvalidDomainException(
                    'The license is for another domain',
                    HttpResponse::HTTP_FORBIDDEN
                );
            }
        }

        $this->storage->disk('local')->put(
            'idir.license',
            rand(1, 3599).'|'.$this->hash->make($current)
        );
    }
}
