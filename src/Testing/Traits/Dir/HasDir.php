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

namespace N1ebieski\IDir\Testing\Traits\Dir;

use N1ebieski\IDir\Models\Category\Dir\Category;

trait HasDir
{
    /**
     * [setUpDir description]
     * @return array [description]
     */
    private function setUpDir(): array
    {
        /** @var Category */
        $category = Category::makeFactory()->active()->create();

        return [
            'title' => 'Dolore deserunt et ex cupidatat.',
            'tags' => ['cupidatat', 'nulla quis', 'magna'],
            'content_html' => 'Aute ipsum laboris ullamco incididunt amet mollit reprehenderit est duis est. Qui fugiat id eu ex eu ex. Magna enim ipsum amet excepteur excepteur qui ad commodo laborum labore velit Lorem sint. Ad nisi dolore commodo non Lorem duis sint quis. Eiusmod sunt eiusmod est deserunt eiusmod reprehenderit est tempor commodo laboris.',
            'categories' => [$category->id],
            'url' => 'https://idir.test'
        ];
    }
}
