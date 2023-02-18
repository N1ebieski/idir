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

namespace N1ebieski\IDir\Tests\Integration\Rules\Backlink;

use Tests\TestCase;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Client as GuzzleClient;
use N1ebieski\IDir\Rules\BacklinkRule;
use Illuminate\Http\Response as HttpResponse;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Http\Clients\DirBacklink\Requests\ShowRequest;

class BacklinkRuleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [protected description]
     * @var string
     */
    private string $url = 'http://asdasjdkasjdkas.pl';

    public function testNoExists(): void
    {
        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_OK, [], 'dadasd asdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $this->app->bind(ShowRequest::class, function ($app, $with) use ($client) {
            return new ShowRequest($with['url'], $client);
        });

        /** @var BacklinkRule */
        $rule = $this->app->make(BacklinkRule::class, [
            'link' => $this->url
        ]);

        $response = $rule->passes('backlink_url', 'http://wewewew.pl');

        $this->assertFalse($response);
    }

    public function testExistsNofollow(): void
    {
        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_OK, [], 'dadasd <a rel="nofollow" href="' . $this->url . '">dadasdasd</a> asdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $this->app->bind(ShowRequest::class, function ($app, $with) use ($client) {
            return new ShowRequest($with['url'], $client);
        });

        /** @var BacklinkRule */
        $rule = $this->app->make(BacklinkRule::class, [
            'link' => $this->url
        ]);

        $response = $rule->passes('backlink_url', 'http://wewewew.pl');

        $this->assertFalse($response);
    }

    public function testExists(): void
    {
        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_OK, [], 'sdadas<a href="' . $this->url . '">dadasdasd</a> sdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $this->app->bind(ShowRequest::class, function ($app, $with) use ($client) {
            return new ShowRequest($with['url'], $client);
        });

        /** @var BacklinkRule */
        $rule = $this->app->make(BacklinkRule::class, [
            'link' => $this->url
        ]);

        $response = $rule->passes('backlink_url', 'http://wewewew.pl');

        $this->assertTrue($response);
    }
}
