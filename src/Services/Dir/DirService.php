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

namespace N1ebieski\IDir\Services\Dir;

use Throwable;
use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Group;
use N1ebieski\IDir\Models\DirStatus;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Contracts\Session\Session;
use N1ebieski\IDir\Models\Field\Dir\Field;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\Services\User\AutoUserFactory;
use N1ebieski\IDir\Services\Payment\Dir\PaymentFactory;

class DirService
{
    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param PaymentFactory $paymentFactory
     * @param AutoUserFactory $userFactory
     * @param Session $session
     * @param Carbon $carbon
     * @param DB $db
     */
    public function __construct(
        protected Dir $dir,
        protected PaymentFactory $paymentFactory,
        protected AutoUserFactory $userFactory,
        protected Session $session,
        protected Carbon $carbon,
        protected DB $db
    ) {
        //
    }

    /**
     * [createOrUpdateSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function createOrUpdateSession(array $attributes): void
    {
        if ($this->session->has($this->sessionName())) {
            $this->updateSession($attributes);
        } else {
            $this->createSession($attributes);
        }
    }

    /**
     * [createSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function createSession(array $attributes): void
    {
        /** @var Field */
        $field = $this->dir->fields()->make();

        $this->session->put($this->sessionName(), array_merge(
            $attributes,
            [
                'field' => $field->setRelations(['morph' => $this->dir])
                    ->makeService()
                    ->prepareValues($attributes['field'] ?? [])
            ]
        ));
    }

    /**
     * [updateSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function updateSession(array $attributes): void
    {
        /** @var Field */
        $field = $this->dir->fields()->make();

        $this->session->put($this->sessionName(), array_merge(
            $this->session->get($this->sessionName()),
            $attributes,
            [
                'field' => $field->setRelations(['morph' => $this->dir])
                    ->makeService()
                    ->prepareValues($attributes['field'] ?? [])
            ],
        ));
    }

    /**
     *
     * @param array $attributes
     * @return Dir
     * @throws Throwable
     */
    public function create(array $attributes): Dir
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->dir->fill($attributes);

            $this->dir->content = $attributes['content_html'];

            /** @var Group */
            $group = $attributes['group'];

            try {
                $this->dir->status = Status::fromString(
                    $attributes['payment_type'] ?? $group->apply_status->getValue()
                );
            } catch (\InvalidArgumentException $e) {
                $this->dir->status = $group->apply_status->getValue();
            }

            $this->dir->user()->associate(
                $attributes['user'] ?? $this->userFactory->makeUser($attributes['email'])
            );

            $this->dir->group()->associate($group);

            $this->dir->save();

            if (array_key_exists('field', $attributes)) {
                /** @var Field */
                $field = $this->dir->fields()->make();

                $field->setRelations(['morph' => $this->dir])
                    ->makeService()
                    ->createValues($attributes['field'] ?? []);
            }

            if (array_key_exists('backlink', $attributes) && array_key_exists('backlink_url', $attributes)) {
                /** @var DirBacklink */
                $dirBacklink = $this->dir->backlink()->make();

                $dirBacklink->makeService()->create([
                    'dir' => $this->dir,
                    'backlink' => $attributes['backlink'],
                    'backlink_url' => $attributes['backlink_url']
                ]);
            }

            if (array_key_exists('url', $attributes)) {
                /** @var DirStatus */
                $dirStatus = $this->dir->status()->make();

                $dirStatus->makeService()->create([
                    'dir' => $this->dir,
                    'url' => $attributes['url']
                ]);
            }

            if (array_key_exists('categories', $attributes)) {
                $this->dir->categories()->attach($attributes['categories'] ?? []);
            }

            if (array_key_exists('tags', $attributes)) {
                $this->dir->tag($attributes['tags'] ?? []);
            }

            if (array_key_exists('payment_type', $attributes)) {
                $this->dir->setRelations([
                    'payment' => $this->paymentFactory->makePayment(
                        $this->dir,
                        $attributes['price']
                    )
                ]);
            }

            return $this->dir;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Dir
     * @throws Throwable
     */
    public function update(array $attributes): Dir
    {
        return $this->db->transaction(function () use ($attributes) {
            if (array_key_exists('url', $attributes)) {
                /** @var DirStatus */
                $dirStatus = $this->dir->status()->make();

                $dirStatus->makeService()->sync([
                    'dir' => $this->dir,
                    'url' => $attributes['url']
                ]);
            }

            if (array_key_exists('field', $attributes)) {
                /** @var Field */
                $field = $this->dir->fields()->make();

                $field->setRelations(['morph' => $this->dir])
                    ->makeService()
                    ->updateValues($attributes['field'] ?? []);
            }

            $this->dir->fill($attributes);

            $this->dir->content = $attributes['content_html'];

            if (array_key_exists('categories', $attributes)) {
                $this->dir->categories()->sync($attributes['categories'] ?? []);
            }

            if (array_key_exists('tags', $attributes)) {
                $this->dir->retag($attributes['tags'] ?? []);
            }

            $this->dir->save();

            return $this->dir;
        });
    }

    /**
     *
     * @param array $attributes
     * @return Dir
     * @throws Throwable
     */
    public function updateFull(array $attributes): Dir
    {
        return $this->db->transaction(function () use ($attributes) {
            if (array_key_exists('field', $attributes)) {
                /** @var Field */
                $field = $this->dir->fields()->make();

                $field->setRelations(['morph' => $this->dir])
                    ->makeService()
                    ->updateValues($attributes['field'] ?? []);
            }

            if (array_key_exists('backlink', $attributes) && array_key_exists('backlink_url', $attributes)) {
                /** @var DirBacklink */
                $dirBacklink = $this->dir->backlink()->make();

                $dirBacklink->makeService()->sync([
                    'dir' => $this->dir,
                    'backlink' => $attributes['backlink'],
                    'backlink_url' => $attributes['backlink_url']
                ]);
            }

            if (array_key_exists('url', $attributes)) {
                /** @var DirStatus */
                $dirStatus = $this->dir->status()->make();

                $dirStatus->makeService()->sync([
                    'dir' => $this->dir,
                    'url' => $attributes['url']
                ]);
            }

            $this->dir->fill($attributes);

            if (array_key_exists('content_html', $attributes)) {
                $this->dir->content = $attributes['content_html'];
            }

            /** @var Group */
            $group = $attributes['group'];

            try {
                $this->dir->status = Status::fromString(
                    $attributes['payment_type'] ?? $group->apply_status->getValue()
                );
            } catch (\InvalidArgumentException $e) {
                $this->dir->status = $group->apply_status->getValue();
            }

            if ($this->dir->group_id !== $group->id) {
                $this->dir->group()->associate($group);
                $this->dir->makeService()->nullablePrivileged();
            }

            if (array_key_exists('user', $attributes)) {
                $this->dir->user()->associate($attributes['user']);
            }

            if (array_key_exists('categories', $attributes)) {
                $this->dir->categories()->sync($attributes['categories'] ?? []);
            }

            if (array_key_exists('tags', $attributes)) {
                $this->dir->retag($attributes['tags'] ?? []);
            }

            if (array_key_exists('payment_type', $attributes)) {
                $this->dir->setRelations([
                    'payment' => $this->paymentFactory->makePayment(
                        $this->dir,
                        $attributes['price']
                    )
                ]);
            }

            $this->dir->save();

            return $this->dir;
        });
    }

    /**
     *
     * @param int $status
     * @return bool
     * @throws Throwable
     */
    public function updateStatus(int $status): bool
    {
        return $this->db->transaction(function () use ($status) {
            return $this->dir->update(['status' => $status]);
        });
    }

    /**
     * [nullPrivileged description]
     * @return bool [description]
     */
    public function nullablePrivileged(): bool
    {
        return $this->db->transaction(function () {
            return $this->dir->update([
                'privileged_at' => null,
                'privileged_to' => null
            ]);
        });
    }

    /**
     * [deactivateByBacklink description]
     * @return bool [description]
     */
    public function deactivateByBacklink(): bool
    {
        return $this->db->transaction(function () {
            return $this->dir->update(['status' => Status::BACKLINK_INACTIVE]);
        });
    }

    /**
     * [deactivateByStatus description]
     * @return bool [description]
     */
    public function deactivateByStatus(): bool
    {
        return $this->db->transaction(function () {
            return $this->dir->update(['status' => Status::STATUS_INACTIVE]);
        });
    }

    /**
     * [deactivateByPayment description]
     * @return bool [description]
     */
    public function deactivateByPayment(): bool
    {
        return $this->db->transaction(function () {
            return $this->dir->update(['status' => Status::PAYMENT_INACTIVE]);
        });
    }

    /**
     * [activate description]
     * @return bool [description]
     */
    public function activate(): bool
    {
        return $this->db->transaction(function () {
            return $this->dir->update(['status' => Status::ACTIVE]);
        });
    }

    /**
     *
     * @param Group $group
     * @return bool
     * @throws Throwable
     */
    public function moveToAltGroup(Group $group): bool
    {
        return $this->db->transaction(function () use ($group) {
            $this->dir->categories()->sync(
                $this->dir->categories
                    ->pluck('id')
                    ->take($group->max_cats)
                    ->toArray()
            );

            return $this->dir->group()->associate($group)->save();
        });
    }

    /**
     *
     * @param int|null $days
     * @return bool
     * @throws Throwable
     */
    public function updatePrivileged(int $days = null): bool
    {
        return $this->db->transaction(function () use ($days) {
            return $this->dir->update([
                'privileged_at' => $this->carbon->now()->format('Y-m-d H:i:s'),
                'privileged_to' => is_int($days) ?
                    (
                        $this->dir->privileged_to !== null ?
                            $this->carbon->parse($this->dir->privileged_to)->addDays($days)
                            : $this->carbon->now()->addDays($days)
                    )
                    : null
            ]);
        });
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete(): bool
    {
        return $this->db->transaction(function () {
            $this->dir->categories()->detach();

            $this->dir->comments()->delete();

            $this->dir->fields()->detach();

            $this->dir->stats()->detach();

            $this->dir->payments()->delete();

            $this->dir->ratings()->delete();

            $this->dir->reports()->delete();

            $this->dir->regions()->detach();

            $this->dir->map()->delete();

            $this->dir->detag();

            $this->dir->delete();

            return true;
        });
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids): int
    {
        return $this->db->transaction(function () use ($ids) {
            $this->dir->categories()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->comments()->make()->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->fields()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->stats()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->payments()->make()->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->ratings()->make()->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->reports()->make()->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->regions()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->tags()->newPivotStatement()
                ->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            $this->dir->map()->make()->whereIn('model_id', $ids)
                ->where('model_type', $this->dir->getMorphClass())->delete();

            return $this->dir->whereIn('id', $ids)->delete();
        });
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function sessionName(): string
    {
        return $this->dir->exists ? 'dirId.' . $this->dir->id : 'dir';
    }
}
