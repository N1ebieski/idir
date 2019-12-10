<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Http\UploadedFile;
use N1ebieski\IDir\Libs\File;
use Carbon\Carbon;

/**
 * [DirService description]
 */
class DirService implements Serviceable
{
    /**
     * Model
     * @var Dir
     */
    protected $dir;

    /**
     * Model
     * @var Payment
     */
    protected $payment;

    /**
     * Model
     * @var Price
     */
    protected $price;

    /**
     * Model
     * @var DirBacklink
     */
    protected $dirBacklink;

    /**
     * [protected description]
     * @var File
     */
    protected $file;

    /**
     * [private description]
     * @var Session
     */
    protected $session;

    /**
     * [private description]
     * @var Storage
     */
    protected $storage;

    /**
     * [protected description]
     * @var string
     */
    protected $img_dir = 'vendor/idir/dirs';

    /**
     * [__construct description]
     * @param Dir         $dir         [description]
     * @param Payment     $payment     [description]
     * @param Price       $price       [description]
     * @param File        $file        [description]
     * @param DirBacklink $dirBacklink [description]
     * @param Session     $session     [description]
     * @param Storage     $storage     [description]
     */
    public function __construct(
        Dir $dir,
        Payment $payment,
        Price $price,
        DirBacklink $dirBacklink,
        File $file,
        Session $session,
        Storage $storage
    )
    {
        $this->dir = $dir;
        $this->dirBacklink = $dirBacklink;
        $this->payment = $payment;
        $this->price = $price;

        $this->file = $file;
        $this->session = $session;
        $this->storage = $storage;
    }

    /**
     * [prepareField description]
     * @param  array $attributes [description]
     * @return array             [description]
     */
    protected function prepareFieldAttribute(array $attributes) : array
    {
        if (isset($attributes['field'])) {
            foreach ($attributes['field'] as $key => $value) {
                if ($value instanceof UploadedFile) {
                    if (is_int($this->dir->id)) {
                        $this->file->setPath($this->img_dir . "/" . $this->dir->id . "/fields/" . $key);
                    }

                    $attributes['field'][$key] = $this->file->setFile($value)->prepare();
                }
            }
        }

        return $attributes;
    }

    /**
     * [makeSessionName description]
     * @return string [description]
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
            $this->prepareFieldAttribute($attributes)
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
            $this->prepareFieldAttribute($attributes) + $this->session->get($this->makeSessionName())
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
        $this->dir->content = $this->dir->content_html;
        $this->dir->status = $this->makeStatus($attributes['payment_type'] ?? null);
        $this->dir->save();

        if (isset($attributes['field'])) {
            $this->createFields($attributes['field']);
        }

        if (isset($attributes['backlink']) && isset($attributes['backlink_url'])) {
            $this->dirBacklink->setDir($this->dir)->makeService()->create($attributes);
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
    protected function createPayment(array $attributes) : Payment
    {
        return $this->payment->setMorph($this->dir)->setPriceMorph(
            $this->price->find($attributes["payment_{$attributes['payment_type']}"])
        )->makeService()->create($attributes);
    }

    /**
     * [createFields description]
     * @param  array $attributes [description]
     * @return int               [description]
     */
    protected function createFields(array $attributes) : int
    {
        $i = 0;

        foreach ($this->dir->getGroup()->fields()->get() as $field) {
            if (isset($attributes[$field->id])) {
                $value = $attributes[$field->id];

                if ($value instanceof UploadedFile) {
                    $this->file->setFile($value);
                    $this->file->setPath($this->img_dir . "/" . $this->dir->id . "/fields/" . $field->id);
                    $this->file->moveFromTemp();

                    $value = $this->file->getFilePath();
                }

                $ids[$field->id] = ['value' => json_encode($value)];
                $i++;
            }
        }

        $this->dir->fields()->attach($ids);

        return $i;
    }

    /**
     * [updateFields description]
     * @param  array $attributes [description]
     * @return int               [description]
     */
    protected function updateFields(array $attributes) : int
    {
        $i = 0;

        foreach ($this->dir->getGroup()->fields()->get() as $field) {
            if ($field->type === 'image') {
                $path = ($img = $this->dir->fields->where('id', $field->id)->first()) !== null ?
                    json_decode($img->pivot->value) : null;
            }

            if (isset($attributes[$field->id])) {
                $value = $attributes[$field->id];

                if ($value instanceof UploadedFile) {
                    $this->file->setFile($value);
                    $this->file->setPath($this->img_dir . "/" . $this->dir->id . "/fields/" . $field->id);

                    if ($path !== ($value = $this->file->getFilePath())) {
                        $this->file->moveFromTemp();

                        $this->storage->disk('public')->delete($path);
                    }
                }

                $ids[$field->id] = ['value' => json_encode($value)];
                $i++;
            } else {
                if ($field->type === 'image') {
                    $this->storage->disk('public')->delete($path);
                }
            }
        }

        $this->dir->fields()->sync($ids);

        return $i;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->dir->fill($attributes);

        if ($this->dir->getGroup()->id !== $this->dir->group_id) {
            $this->dir->group()->associate($this->dir->getGroup());
            $this->dir->makeRepo()->nullablePrivileged();
        }
        $this->dir->content = $this->dir->content_html;
        $this->dir->status = $this->makeStatus($attributes['payment_type'] ?? null);

        if (isset($attributes['field'])) {
            $this->updateFields($attributes['field']);
        }

        if (isset($attributes['backlink'])) {
            $this->dirBacklink->setDir($this->dir)->makeService()->sync($attributes);
        }

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
     * [updateStatus description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updateStatus(array $attributes) : bool
    {

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

    }
}
