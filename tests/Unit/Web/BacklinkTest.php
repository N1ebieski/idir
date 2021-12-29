<?php

namespace N1ebieski\IDir\Tests\Unit\Web;

use Tests\TestCase;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use Illuminate\Support\Facades\App;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use N1ebieski\IDir\Http\Clients\Dir\BacklinkClient;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
            new GuzzleResponse(200, [], 'dadasd <a rel="nofollow" href="' . $this->url . '">dadasdasd</a> asdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $rule = App::make(\N1ebieski\IDir\Rules\BacklinkRule::class, [
            'link' => $this->url,
            'client' => new BacklinkClient($client)
        ]);
        $response = $rule->passes(null, 'http://wewewew.pl');

        $this->assertTrue($response === 0);
    }

    public function testRuleBacklinkPass()
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, [], 'sdadas<a href="' . $this->url . '">dadasdasd</a> sdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $rule = App::make(\N1ebieski\IDir\Rules\BacklinkRule::class, [
            'link' => $this->url,
            'client' => new BacklinkClient($client)
        ]);
        $response = $rule->passes(null, 'http://wewewew.pl');

        $this->assertTrue($response === 1);
    }
}
