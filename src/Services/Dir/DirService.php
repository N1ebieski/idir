<?php

namespace N1ebieski\IDir\Services\Dir;

use Illuminate\Support\Carbon;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Session\Session;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Services\User\AutoUserFactory;
use Illuminate\Contracts\Container\Container as App;
use N1ebieski\IDir\Services\Payment\Dir\PaymentFactory;
use N1ebieski\ICore\Services\Interfaces\CreateInterface;
use N1ebieski\ICore\Services\Interfaces\DeleteInterface;
use N1ebieski\ICore\Services\Interfaces\UpdateInterface;
use N1ebieski\ICore\Services\Interfaces\FullUpdateInterface;
use N1ebieski\ICore\Services\Interfaces\GlobalDeleteInterface;
use N1ebieski\ICore\Services\Interfaces\StatusUpdateInterface;

/**
 *
 * @author Mariusz Wysokiński <kontakt@intelekt.net.pl>
 */
class DirService implements
    CreateInterface,
    UpdateInterface,
    StatusUpdateInterface,
    FullUpdateInterface,
    DeleteInterface,
    GlobalDeleteInterface
{
    /**
     * Model
     * @var Dir
     */
    protected $dir;

    /**
     * Undocumented variable
     *
     * @var PaymentFactory
     */
    protected $paymentFactory;

    /**
     * Undocumented variable
     *
     * @var AutoUserFactory
     */
    protected $userFactory;

    /**
     * [private description]
     * @var Session
     */
    protected $session;

    /**
     * Undocumented variable
     *
     * @var Carbon
     */
    protected $carbon;

    /**
     * Undocumented variable
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Undocumented variable
     *
     * @var DB
     */
    protected $db;

    /**
     * Undocumented variable
     *
     * @var App
     */
    protected $app;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param PaymentFactory $paymentFactory
     * @param AutoUserFactory $userFactory
     * @param Session $session
     * @param Carbon $carbon
     * @param Auth $auth
     * @param DB $db
     */
    public function __construct(
        Dir $dir,
        PaymentFactory $paymentFactory,
        AutoUserFactory $userFactory,
        Session $session,
        Carbon $carbon,
        Auth $auth,
        DB $db
    ) {
        $this->dir = $dir;

        $this->paymentFactory = $paymentFactory;
        $this->userFactory = $userFactory;

        $this->session = $session;
        $this->carbon = $carbon;
        $this->auth = $auth;
        $this->db = $db;
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
        $this->session->put($this->sessionName(), array_merge(
            $attributes,
            [
                'field' => $this->dir->fields()->make()
                    ->setRelations(['morph' => $this->dir])
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
        $this->session->put($this->sessionName(), array_merge(
            $this->session->get($this->sessionName()),
            $attributes,
            [
                'field' => $this->dir->fields()->make()
                    ->setRelations(['morph' => $this->dir])
                    ->makeService()
                    ->prepareValues($attributes['field'] ?? [])
            ],
        ));
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes): Model
    {
        return $this->db->transaction(function () use ($attributes) {
            $this->dir->fill($attributes);

            $this->dir->content = $attributes['content_html'];

            try {
                $this->dir->status = Status::fromString(
                    $attributes['payment_type'] ?? $this->dir->group->apply_status->getValue()
                );
            } catch (\InvalidArgumentException $e) {
                $this->dir->status = $this->dir->group->apply_status->getValue();
            }

            $this->dir->user()->associate(
                $this->auth->user() ?? $this->makeUser($attributes)
            );

            $this->dir->group()->associate($this->dir->group);

            $this->dir->save();

            if (array_key_exists('field', $attributes)) {
                $this->dir->fields()->make()
                    ->setRelations(['morph' => $this->dir])
                    ->makeService()
                    ->createValues($attributes['field'] ?? []);
            }

            if (array_key_exists('backlink', $attributes) && array_key_exists('backlink_url', $attributes)) {
                $this->dir->backlink()->make()
                    ->setRelations(['dir' => $this->dir])
                    ->makeService()
                    ->create([
                        'backlink' => $attributes['backlink'],
                        'backlink_url' => $attributes['backlink_url']
                    ]);
            }

            if (array_key_exists('url', $attributes)) {
                $this->dir->status()->make()
                    ->setRelations(['dir' => $this->dir])
                    ->makeService()
                    ->create([
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
                $this->dir->setRelations(['payment' => $this->makePayment($attributes)]);
            }

            return $this->dir;
        });
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            if (array_key_exists('url', $attributes)) {
                $this->dir->status()->make()
                    ->setRelations(['dir' => $this->dir])
                    ->makeService()
                    ->sync([
                        'url' => $attributes['url']
                    ]);
            }

            if (array_key_exists('field', $attributes)) {
                $this->dir->fields()->make()
                    ->setRelations(['morph' => $this->dir])
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

            return $this->dir->save();
        });
    }

    /**
     * [updateFull description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateFull(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            if (array_key_exists('field', $attributes)) {
                $this->dir->fields()->make()
                    ->setRelations(['morph' => $this->dir])
                    ->makeService()
                    ->updateValues($attributes['field'] ?? []);
            }

            if (array_key_exists('backlink', $attributes) && array_key_exists('backlink_url', $attributes)) {
                $this->dir->backlink()->make()
                    ->setRelations(['dir' => $this->dir])
                    ->makeService()
                    ->sync([
                        'backlink' => $attributes['backlink'],
                        'backlink_url' => $attributes['backlink_url']
                    ]);
            }

            if (array_key_exists('url', $attributes)) {
                $this->dir->status()->make()
                    ->setRelations(['dir' => $this->dir])
                    ->makeService()
                    ->sync([
                        'url' => $attributes['url']
                    ]);
            }

            $this->dir->fill($attributes);

            if (array_key_exists('content_html', $attributes)) {
                $this->dir->content = $attributes['content_html'];
            }

            try {
                $this->dir->status = Status::fromString(
                    $attributes['payment_type'] ?? $this->dir->group->apply_status->getValue()
                );
            } catch (\InvalidArgumentException $e) {
                $this->dir->status = $this->dir->group->apply_status->getValue();
            }

            if ($this->dir->group_id !== $this->dir->group->id) {
                $this->dir->group()->associate($this->dir->group);
                $this->dir->makeRepo()->nullablePrivileged();
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
                $this->dir->setRelations(['payment' => $this->makePayment($attributes)]);
            }

            return $this->dir->save();
        });
    }

    /**
     * Update Status attribute the specified Dir in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->dir->update(['status' => (int)$attributes['status']]);
        });
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function moveToAltGroup(): bool
    {
        return $this->db->transaction(function () {
            $this->dir->categories()->sync(
                $this->dir->categories
                    ->pluck('id')
                    ->take($this->dir->group->max_cats)
                    ->toArray()
            );

            return $this->dir->group()->associate($this->dir->group)->save();
        });
    }

    /**
     * Update Status attribute the specified Dir in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePrivileged(array $attributes): bool
    {
        return $this->db->transaction(function () use ($attributes) {
            return $this->dir->update([
                'privileged_at' => $this->carbon->now()->format('Y-m-d H:i:s'),
                'privileged_to' => is_int($attributes['days']) ?
                    (
                        $this->dir->privileged_to !== null ?
                            $this->carbon->parse($this->dir->privileged_to)->addDays($attributes['days'])
                            : $this->carbon->now()->addDays($attributes['days'])
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

            return $this->dir->delete();
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
     * @param array $attributes
     * @return Payment
     */
    public function makePayment(array $attributes): Payment
    {
        return $this->paymentFactory->makePayment(
            $this->dir,
            $attributes["payment_{$attributes['payment_type']}"],
            $attributes['payment_type']
        );
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function sessionName(): string
    {
        return is_int($this->dir->id) ? 'dirId.' . $this->dir->id : 'dir';
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return User
     */
    protected function makeUser(array $attributes): User
    {
        return $this->userFactory->makeUser($attributes['email']);
    }
}