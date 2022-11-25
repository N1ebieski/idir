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

namespace N1ebieski\IDir\Listeners\Stat\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Events\Dispatcher;
use N1ebieski\ICore\Utils\MigrationUtil;
use N1ebieski\IDir\Models\Stat\Dir\Stat;
use Illuminate\Database\Eloquent\Builder;
use N1ebieski\ICore\ValueObjects\Stat\Slug;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use N1ebieski\IDir\Events\Interfaces\Dir\DirEventInterface;
use N1ebieski\IDir\Events\Interfaces\Dir\DirCollectionEventInterface;

class IncrementView
{
    /**
     *
     * @param Stat $stat
     * @param MigrationUtil $migrationUtil
     * @return void
     */
    public function __construct(
        protected Stat $stat,
        protected MigrationUtil $migrationUtil
    ) {
        //
    }

    /**
     *
     * @param Dir $dir
     * @return bool
     */
    public function verify(Dir $dir): bool
    {
        return $dir->status->isActive()
            && $this->migrationUtil->contains('copy_view_to_visit_in_stats_table');
    }

    /**
     * Handle the event.
     *
     * @param  DirEventInterface  $event
     * @return void
     */
    public function handleSingle($event): void
    {
        if (!$this->verify($event->dir)) {
            return;
        }

        /** @var Stat */
        $stat = $this->stat->makeCache()->rememberBySlug(Slug::VIEW);

        $stat->setRelations(['morph' => $event->dir])
            ->makeService()
            ->increment();
    }

    /**
     * Handle the event.
     *
     * @param  DirCollectionEventInterface  $event
     * @return void
     */
    public function handleGlobal($event): void
    {
        /** @var Collection */
        $morphs = $event->dirs->load([
            'stats' => function (MorphToMany|Builder $query) {
                return $query->where('slug', Slug::VIEW);
            }
        ])
        ->filter(fn (Dir $dir) => $this->verify($dir));

        if ($morphs->isNotEmpty()) {
            /** @var Stat */
            $stat = $this->stat->makeCache()->rememberBySlug(Slug::VIEW);

            $stat->setRelations(['morphs' => $morphs])
                ->makeService()
                ->incrementGlobal();
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            [
                \N1ebieski\IDir\Events\Web\Dir\ShowEvent::class
            ],
            [$this::class, 'handleSingle']
        );

        $events->listen(
            [
                \N1ebieski\IDir\Events\Web\Home\IndexEvent::class,
                \N1ebieski\IDir\Events\Web\Dir\IndexEvent::class,
                \N1ebieski\IDir\Events\Web\Dir\SearchEvent::class,
                \N1ebieski\IDir\Events\Web\Category\Dir\ShowEvent::class,
                \N1ebieski\IDir\Events\Web\Tag\Dir\ShowEvent::class,
                \N1ebieski\IDir\Events\Api\Dir\IndexEvent::class
            ],
            [$this::class, 'handleGlobal']
        );
    }
}
