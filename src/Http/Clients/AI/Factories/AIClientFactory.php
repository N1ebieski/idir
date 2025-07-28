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

namespace N1ebieski\IDir\Http\Clients\AI\Factories;

use N1ebieski\IDir\ValueObjects\AI\Driver;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Http\Clients\AI\Interfaces\AIClientInterface;

class AIClientFactory
{
    public function __construct(protected App $app)
    {
        //
    }

    public function makeClient(Driver $driver): AIClientInterface
    {
        return match ($driver) {
            Driver::OpenAI => $this->app->make(\N1ebieski\IDir\Http\Clients\AI\OpenAI\OpenAIClient::class),

            default => throw new \N1ebieski\IDir\Exceptions\AI\DriverNotFoundException(
                "Driver {$driver->value} not found",
                HttpResponse::HTTP_FORBIDDEN
            )
        };
    }
}
