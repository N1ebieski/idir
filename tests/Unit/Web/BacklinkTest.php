<?php

namespace N1ebieski\IDir\Tests\Unit\Web;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Facades\App;

/**
 * [BacklinkTest description]
 */
class BacklinkTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * [protected description]
     * @var string
     */
    protected $url = 'http://asdasjdkasjdkas.pl';

    public function test_rule_backlink_nofollow_fail()
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, [], 'dadasd <a rel="nofollow" href="' . $this->url . '">dadasdasd</a> asdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $rule = App::make(\N1ebieski\IDir\Rules\BacklinkRule::class, [
            'link' => $this->url,
            'guzzle' => $client
        ]);
        $response = $rule->passes(null, '/');

        $this->assertTrue($response === 0);
    }

    public function test_rule_backlink_pass()
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, [], 'sdadas<a href="' . $this->url . '">dadasdasd</a> sdasdasd')
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);

        $rule = App::make(\N1ebieski\IDir\Rules\BacklinkRule::class, [
            'link' => $this->url,
            'guzzle' => $client
        ]);
        $response = $rule->passes(null, '/');

        $this->assertTrue($response === 1);
    }

}
