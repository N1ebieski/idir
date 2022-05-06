<?php

namespace N1ebieski\IDir\Tests\Unit\Web;

use Tests\TestCase;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Response as HttpResponse;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use N1ebieski\IDir\Http\Clients\DirBacklink\Requests\ShowRequest;

class BacklinkTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [protected description]
     * @var string
     */
    protected $url = 'http://asdasjdkasjdkas.pl';

    public function testRuleBacklinkNofollowFail()
    {
        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_OK, [], 'dadasd <a rel="nofollow" href="' . $this->url . '">dadasdasd</a> asdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $this->app->bind(ShowRequest::class, function ($app, $with) use ($client) {
            return new ShowRequest($with['url'], $client);
        });

        $rule = $this->app->make(\N1ebieski\IDir\Rules\BacklinkRule::class, [
            'link' => $this->url
        ]);

        $response = $rule->passes(null, 'http://wewewew.pl');

        $this->assertTrue($response === 0);
    }

    public function testRuleBacklinkPass()
    {
        $mock = new MockHandler([
            new GuzzleResponse(HttpResponse::HTTP_OK, [], 'sdadas<a href="' . $this->url . '">dadasdasd</a> sdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $this->app->bind(ShowRequest::class, function ($app, $with) use ($client) {
            return new ShowRequest($with['url'], $client);
        });

        $rule = $this->app->make(\N1ebieski\IDir\Rules\BacklinkRule::class, [
            'link' => $this->url
        ]);

        $response = $rule->passes(null, 'http://wewewew.pl');

        $this->assertTrue($response === 1);
    }
}
