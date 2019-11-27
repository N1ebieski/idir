<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as Collect;
use N1ebieski\IDir\Models\Payment\Dir\Payment;
use N1ebieski\IDir\Models\Price;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\DirBacklink;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Http\UploadedFile;
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
     * [private description]
     * @var Collect
     */
    protected $collect;

    /**
     * [protected description]
     * @var string
     */
    protected $img_temp_dir = 'vendor/idir/dirs/temp';

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
     * @param DirBacklink $dirBacklink [description]
     * @param Session     $session     [description]
     * @param Storage     $storage     [description]
     * @param Collect     $collect     [description]
     */
    public function __construct(
        Dir $dir,
        Payment $payment,
        Price $price,
        DirBacklink $dirBacklink,
        Session $session,
        Storage $storage,
        Collect $collect
    )
    {
        $this->dir = $dir;
        $this->dirBacklink = $dirBacklink;
        $this->payment = $payment;
        $this->price = $price;

        $this->session = $session;
        $this->storage = $storage;
        $this->collect = $collect;
    }

    /**
     * [prepareField description]
     * @param  array $attributes [description]
     * @return array             [description]
     */
    protected function prepareFieldAttribute(array $attributes) : array
    {
        foreach ($attributes as $key => $value) {
            if ($value instanceof UploadedFile) {
                $attributes[$key] = $this->prepareImage($value, 'field.'.$key);
            }
        }

        return $attributes;
    }

    /**
     * [uploadImage description]
     * @param  UploadedFile      $img  [description]
     * @param  string|null       $path [description]
     * @return string                  [description]
     */
    protected function prepareImage(UploadedFile $img, string $path = null) : string
    {
        $path = $this->img_temp_dir . '/' . $path;

        if ($this->storage->disk('public')->exists($exist = $path . '/' . $img->getClientOriginalName())) {
            return $exist;
        }

        return $this->uploadImage($img, $path);
    }

    /**
     * [moveImage description]
     * @param  UploadedFile $img  [description]
     * @param  string|null       $path [description]
     * @return string             [description]
     */
    protected function moveImage(UploadedFile $img, string $path = null) : string
    {
        $old = $this->prepareImage($img, $path);
        $new = str_replace('temp', $this->dir->id, $old);

        $this->storage->disk('public')->move($old, $new);

        return $new;
    }

    /**
     * [uploadImage description]
     * @param  UploadedFile      $img  [description]
     * @param  string|null       $path [description]
     * @return string                  [description]
     */
    protected function uploadImage(UploadedFile $img, string $path = null) : string
    {
        return $this->storage->disk('public')->putFile($path, $img);
    }

    /**
     * [createOrUpdateSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function createOrUpdateSession(array $attributes) : void
    {
        if ($this->session->has('dir')) {
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
        $this->session->put('dir', $this->collect->make($attributes)->except(['field'])->toArray());

        if (isset($attributes['field'])) {
            $this->session->put('dir.field', $this->prepareFieldAttribute($attributes['field']));
        }
    }

    /**
     * [updateSession description]
     * @param  array $attributes [description]
     * @return void              [description]
     */
    public function updateSession(array $attributes) : void
    {
        $this->session->put('dir', array_merge(
            $this->collect->make($attributes)->except(['field'])->toArray() + $this->session->get('dir')
        ));

        if (isset($attributes['field'])) {
            $this->session->put('dir.field', $this->prepareFieldAttribute($attributes['field']) + $this->session->get('dir.field'));
        }
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

        foreach ($attributes as $key => $value) {
            if ($value instanceof UploadedFile) {
                $value = $this->moveImage($value, 'field.'.$key);
            }
            $this->dir->fields()->attach($key, ['value' => json_encode($value)]);

            $i++;
        }

        return $i;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {

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
