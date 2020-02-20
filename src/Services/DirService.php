<?php

namespace N1ebieski\IDir\Services;

use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Contracts\Session\Session;
use Carbon\Carbon;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use N1ebieski\ICore\Services\Interfaces\StatusUpdatable;
use N1ebieski\ICore\Services\Interfaces\FullUpdatable;
use N1ebieski\ICore\Services\Interfaces\Deletable;
use N1ebieski\ICore\Services\Interfaces\GlobalDeletable;

/**
 * [DirService description]
 */
class DirService implements
    Creatable,
    Updatable,
    StatusUpdatable,
    FullUpdatable,
    Deletable,
    GlobalDeletable
{
    /**
     * Model
     * @var Dir
     */
    protected $dir;

    /**
     * Model
     * @var Price
     */
    protected $price;

    /**
     * [private description]
     * @var Session
     */
    protected $session;

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @param Price $price
     * @param Session $session
     */
    public function __construct(
        Dir $dir,
        Price $price,
        Session $session
    ) {
        $this->dir = $dir;
        $this->price = $price;

        $this->session = $session;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    protected function makeSessionName() : string
    {
        return is_int($this->dir->id) ? 'dirId.' . $this->dir->id : 'dir';
    }

    /**
     * [createOrUpdateSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function createOrUpdateSession(array $attributes) : void
    {
        if ($this->session->has($this->makeSessionName())) {
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
    public function createSession(array $attributes) : void
    {
        $this->session->put(
            $this->makeSessionName(),
            $this->dir->fields()->make()
                ->setMorph($this->dir)
                ->makeService()
                ->prepareFieldAttribute($attributes)
        );
    }

    /**
     * [updateSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function updateSession(array $attributes) : void
    {
        $this->session->put(
            $this->makeSessionName(),
            $this->dir->fields()->make()
                ->setMorph($this->dir)
                ->makeService()
                ->prepareFieldAttribute($attributes)
            + $this->session->get($this->makeSessionName())
        );
    }

    /**
     * [makeStatus description]
     * @param  string|null  $payment_type  [description]
     * @return int [description]
     */
    protected function makeStatus(string $payment_type = null) : int
    {
        if ($payment_type === 'transfer') {
            return 2;
        }

        return $this->dir->getGroup()->apply_status === 1 ? 1 : 0;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->dir->fill($attributes);
        $this->dir->user()->associate(auth()->user());
        $this->dir->group()->associate($this->dir->getGroup());
        $this->dir->content = $attributes['content_html'];
        $this->dir->status = $this->makeStatus($attributes['payment_type'] ?? null);
        $this->dir->save();

        if (isset($attributes['field'])) {
            $this->dir->fields()->make()
                ->setMorph($this->dir)
                ->makeService()
                ->createValues($attributes['field']);
        }

        if (isset($attributes['backlink']) && isset($attributes['backlink_url'])) {
            $this->dir->backlink()->make()
                ->setDir($this->dir)
                ->makeService()
                ->create($attributes);
        }

        if (isset($attributes['url'])) {
            $this->dir->status()->make()
                ->setDir($this->dir)
                ->makeService()
                ->create($attributes);
        }

        $this->dir->categories()->attach($attributes['categories']);

        $this->dir->tag($attributes['tags'] ?? []);

        if (isset($attributes['payment_type'])) {
            $this->dir->setPayment($this->createPayment($attributes));
        }

        return $this->dir;
    }

    /**
     * [createPayment description]
     * @param  array   $attributes [description]
     * @return Payment             [description]
     */
    public function createPayment(array $attributes) : Payment
    {
        return $this->dir->payments()->make()
            ->setMorph($this->dir)
            ->setPriceMorph(
                $this->price->find($attributes["payment_{$attributes['payment_type']}"])
            )
            ->makeService()
            ->create($attributes);
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->dir->fill($attributes);
        $this->dir->content = $attributes['content_html'];

        if (isset($attributes['field'])) {
            $this->dir->fields()->make()
                ->setMorph($this->dir)
                ->makeService()
                ->updateValues($attributes['field']);
        }

        $this->dir->status()->make()
            ->setDir($this->dir)
            ->makeService()
            ->sync($attributes);

        $this->dir->categories()->sync($attributes['categories']);

        $this->dir->retag($attributes['tags'] ?? []);

        return $this->dir->save();
    }

    /**
     * [updateFull description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateFull(array $attributes) : bool
    {
        $this->dir->fill($attributes);

        if (!$this->dir->isGroup($this->dir->getGroup()->id)) {
            $this->dir->group()->associate($this->dir->getGroup());
            $this->dir->makeRepo()->nullablePrivileged();
        }
        $this->dir->content = $attributes['content_html'];
        $this->dir->status = $this->makeStatus($attributes['payment_type'] ?? null);

        if (isset($attributes['field'])) {
            $this->dir->fields()->make()
                ->setMorph($this->dir)
                ->makeService()
                ->updateValues($attributes['field']);
        }

        if (isset($attributes['backlink'])) {
            $this->dir->backlink()->make()
                ->setDir($this->dir)
                ->makeService()
                ->sync($attributes);
        }

        $this->dir->status()->make()
            ->setDir($this->dir)
            ->makeService()
            ->sync($attributes);

        $this->dir->categories()->sync($attributes['categories']);

        $this->dir->retag($attributes['tags'] ?? []);

        if (isset($attributes['payment_type'])) {
            $this->dir->setPayment($this->createPayment($attributes));
        }

        return $this->dir->save();
    }

    /**
     * Update Status attribute the specified Dir in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {
        return $this->dir->update(['status' => (int)$attributes['status']]);
    }

    /**
     * Update Status attribute the specified Dir in storage.
     *
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePrivileged(array $attributes) : bool
    {
        return $this->dir->update([
            'privileged_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'privileged_to' => is_int($attributes['days']) ?
                $this->dir->privileged_to !== null ?
                    Carbon::parse($this->dir->privileged_to)->addDays($attributes['days'])
                    : Carbon::now()->addDays($attributes['days'])
                : null
        ]);
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        $this->dir->categories()->detach();

        $this->dir->comments()->delete();

        $this->dir->fields()->detach();

        $this->dir->payments()->delete();

        $this->dir->ratings()->delete();

        $this->dir->reports()->delete();

        $this->dir->regions()->delete();

        $this->dir->map()->delete();

        $this->dir->detag();

        return $this->dir->delete();
    }

    /**
     * [deleteGlobal description]
     * @param  array $ids [description]
     * @return int        [description]
     */
    public function deleteGlobal(array $ids) : int
    {
        $this->dir->categories()->newPivotStatement()
            ->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        $this->dir->comments()->make()->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        $this->dir->fields()->newPivotStatement()
            ->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        $this->dir->payments()->make()->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        $this->dir->ratings()->make()->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        $this->dir->reports()->make()->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        $this->dir->regions()->newPivotStatement()
            ->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        $this->dir->tags()->newPivotStatement()
            ->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        $this->dir->map()->make()->whereIn('model_id', $ids)
            ->where('model_type', 'N1ebieski\IDir\Models\Dir')->delete();

        return $this->dir->whereIn('id', $ids)->delete();
    }
}
