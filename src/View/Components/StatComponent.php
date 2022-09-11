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

namespace N1ebieski\IDir\View\Components;

use Illuminate\View\Component;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\View\View;
use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\ICore\Models\Comment\Comment;
use N1ebieski\ICore\Cache\Session\SessionCache;
use N1ebieski\IDir\Models\Category\Dir\Category;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N1ebieski\ICore\ValueObjects\Comment\Status as CommentStatus;
use N1ebieski\ICore\ValueObjects\Category\Status as CategoryStatus;

class StatComponent extends Component
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Category $category
     * @param Comment $comment
     * @param SessionCache $sessionCache
     * @param MigrationUtil $migrationUtil
     * @param Config $config
     * @param ViewFactory $view
     */
    public function __construct(
        protected Dir $dir,
        protected Category $category,
        protected Comment $comment,
        protected SessionCache $sessionCache,
        protected MigrationUtil $migrationUtil,
        protected Config $config,
        protected ViewFactory $view
    ) {
        //
    }

    protected function verifySession(): bool
    {
        return $this->migrationUtil->contains('create_sessions_table')
            && $this->config->get('session.driver') === 'database';
    }

    /**
     * Undocumented function
     *
     * @return View
     */
    public function render(): View
    {
        return $this->view->make('idir::web.components.stat', [
            'countCategories' => $this->category->makeCache()->rememberCountByStatus()
                ->firstWhere('status', CategoryStatus::active()),

            'countDirs' => $this->dir->makeCache()->rememberCountByStatus(),

            'countComments' => $this->comment->makeCache()->rememberCountByModelTypeAndStatus()
                ->where('status', CommentStatus::active()),

            'lastActivity' => $this->dir->makeCache()->rememberLastActivity(),

            'countUsers' => $this->verifySession() ?
                $this->sessionCache->rememberCountByType()
                : null
        ]);
    }
}
