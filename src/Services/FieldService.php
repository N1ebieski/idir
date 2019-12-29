<?php

namespace N1ebieski\IDir\Services;

use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\ICore\Services\Serviceable;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\IDir\Utils\File;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Container\Container as App;

/**
 * [FieldService description]
 */
class FieldService implements Serviceable
{
    /**
     * Model
     * @var Field
     */
    protected Field $field;

    /**
     * [private description]
     * @var Storage
     */
    protected Storage $storage;

    /**
     * [protected description]
     * @var App
     */
    protected App $app;

    /**
     * [protected description]
     * @var string
     */
    protected string $file_dir = 'vendor/idir/dirs/fields';

    /**
     * [__construct description]
     * @param Field       $field       [description]
     * @param Storage     $storage     [description]
     * @param App         $app         [description]
     */
    public function __construct(Field $field, Storage $storage, App $app)
    {
        $this->field = $field;

        $this->storage = $storage;
        $this->app = $app;
    }

    /**
     * [makePath description]
     * @param  int    $id [description]
     * @return string     [description]
     */
    protected function makePath(int $id) : string
    {
        return $this->file_dir . "/" . $id . "/" . $this->field->poli . "/" . $this->field->getMorph()->id;
    }

    /**
     * [prepareField description]
     * @param  array $attributes [description]
     * @return array             [description]
     */
    public function prepareFieldAttribute(array $attributes) : array
    {
        if (isset($attributes['field'])) {
            foreach ($attributes['field'] as $key => $value) {
                if ($value instanceof UploadedFile) {
                    $file = $this->app->make(File::class, [
                        'file' => $value,
                        'path' => is_int($this->field->getMorph()->id) ? $this->makePath($key) : null
                    ]);

                    $attributes['field'][$key] = $file->prepare();
                }
            }
        }

        return $attributes;
    }

    /**
     * [createValues description]
     * @param  array $attributes [description]
     * @return int               [description]
     */
    public function createValues(array $attributes) : int
    {
        $i = 0;

        foreach ($this->field->getMorph()->getGroup()->fields()->get() as $field) {
            if (isset($attributes[$field->id])) {
                $value = $attributes[$field->id];

                if ($value instanceof UploadedFile) {
                    $file = $this->app->make(File::class, [
                        'file' => $value,
                        'path' => $this->makePath($field->id)
                    ]);

                    $file->prepare();
                    $file->moveFromTemp();

                    $value = $file->getFilePath();
                }

                $ids[$field->id] = ['value' => json_encode($value)];
                $i++;
            }
        }

        $this->field->getMorph()->fields()->attach($ids);

        return $i;
    }

    /**
     * [updateValues description]
     * @param  array $attributes [description]
     * @return int               [description]
     */
    public function updateValues(array $attributes) : int
    {
        $i = 0;

        foreach ($this->field->getMorph()->getGroup()->fields()->get() as $field) {
            if ($field->type === 'image') {
                $path = optional($this->field->getMorph()->fields->where('id', $field->id)->first())->decode_value;
            }

            if (isset($attributes[$field->id])) {
                $value = $attributes[$field->id];

                if ($value instanceof UploadedFile) {
                    $file = $this->app->make(File::class, [
                        'file' => $value,
                        'path' => $this->makePath($field->id)
                    ]);

                    if ($path !== $file->getFilePath()) {
                        $file->prepare();
                        $file->moveFromTemp();

                        $this->storage->disk('public')->delete($path);
                    }

                    $value = $file->getFilePath();
                }

                $ids[$field->id] = ['value' => json_encode($value)];
                $i++;
            } else {
                if ($field->type === 'image') {
                    $this->storage->disk('public')->delete($path);
                }
            }
        }

        $this->field->getMorph()->fields()->sync($ids);

        return $i;
    }

    /**
     * [create description]
     * @param  array $attributes [description]
     * @return Model             [description]
     */
    public function create(array $attributes) : Model
    {
        $this->field->fill($attributes);
        $this->field->options = array_merge($attributes[$attributes['type']], ['required' => $attributes['required']]);
        $this->field->save();

        $this->field->morphs()->attach($attributes['morphs']);

        return $this->field;
    }

    /**
     * [update description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function update(array $attributes) : bool
    {
        $this->field->fill($attributes);
        $this->field->options = array_merge($attributes[$attributes['type']], ['required' => $attributes['required']]);

        $this->field->morphs()->sync($attributes['morphs']);

        return $this->field->save();
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
     * [updatePosition description]
     * @param  array $attributes [description]
     * @return bool              [description]
     */
    public function updatePosition(array $attributes) : bool
    {
        return $this->field->update(['position' => (int)$attributes['position']]);
    }

    /**
     * [delete description]
     * @return bool [description]
     */
    public function delete() : bool
    {
        //
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
