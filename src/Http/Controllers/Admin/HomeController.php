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

namespace N1ebieski\IDir\Http\Controllers\Admin;

use N1ebieski\IDir\Models\Dir;
use N1ebieski\ICore\Models\Post;
use Illuminate\Support\Facades\App;
use N1ebieski\ICore\Models\Page\Page;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use N1ebieski\ICore\Http\Clients\Intelekt\Post\PostClient;
use N1ebieski\IDir\Http\Responses\Data\Chart\Dir\GroupData as DirGroupData;
use N1ebieski\IDir\Http\Responses\Data\Chart\Dir\StatusData as DirStatusData;
use N1ebieski\IDir\Http\Responses\Data\Chart\Dir\TimelineData as DirTimelineData;
use N1ebieski\ICore\Http\Responses\Data\Chart\Post\TimelineData as PostAndPagesTimelineData;

class HomeController
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Post $post
     * @param Page $page
     * @param PostClient $client
     * @return HttpResponse
     */
    public function index(Dir $dir, Post $post, Page $page, PostClient $client): HttpResponse
    {
        try {
            $posts = $client->index(['filter' => [
                'status' => 1,
                'orderby' => 'created_at|desc',
                'search' => 'idir',
            ]])->data;
        } catch (\N1ebieski\ICore\Exceptions\Client\TransferException $e) {
            $posts = null;
        }

        return Response::view('idir::admin.home.index', [
            'posts' => $posts,
            'countDirsByStatus' => App::make(DirStatusData::class)
                ->toArray($dir->makeRepo()->countByStatus()),
            'countDirsByGroup' => App::make(DirGroupData::class)
                ->toArray($dir->makeRepo()->countByGroup()),
            'countDirsByDateAndGroup' => App::make(DirTimelineData::class)
                ->toArray($dir->makeRepo()->countByDateAndGroup()),
            'countPostsAndPagesByDate' => App::make(PostAndPagesTimelineData::class)
                ->toArray($post->makeRepo()->countActiveByDateUnionPages($page->activeByDate()))
        ]);
    }
}
