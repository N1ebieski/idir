<?php

namespace N1ebieski\IDir\Services;

use Illuminate\Http\UploadedFile;
use N1ebieski\IDir\Utils\FileUtil;
use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\Eloquent\Model;
use N1ebieski\ICore\Services\Interfaces\Creatable;
use N1ebieski\ICore\Services\Interfaces\Updatable;
use Illuminate\Contracts\Container\Container as App;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use N1ebieski\ICore\Services\Interfaces\PositionUpdatable;

/**
 * [FieldService description]
 */
class FieldService implements Creatable, Updatable, PositionUpdatable
{
    /**
     * Model
     * @var Field
     */
    protected $field;

    /**
     * [private description]
     * @var Storage
     */
    protected $storage;

    /**
     * [protected description]
     * @var App
     */
    protected $app;

    /**
     * [protected description]
     * @var string
     */
    protected $file_dir = 'vendor/idir/dirs/fields';

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
        return $this->file_dir . "/" . $id . "/" . $this->field->poli . "/" . $this->field->morph->id;
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
                    $file = $this->app->make(FileUtil::class, [
                        'file' => $value,
                        'path' => is_int($this->field->morph->id) ? $this->makePath($key) : null
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

        foreach ($this->field->morph->group->fields()->get() as $field) {
            if (isset($attributes[$field->id])) {
                if ($field->type === 'regions') {
                    $this->createRegionsValue($attributes[$field->id]);
                }
    
                if ($field->type === 'map') {
                    $this->updateOrCreateMapValue($attributes[$field->id]);
                }

                $value = $attributes[$field->id];

                if ($value instanceof UploadedFile) {
                    $file = $this->app->make(FileUtil::class, [
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

        $this->field->morph->fields()->attach($ids ?? []);

        return $i;
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    protected function updateOrCreateMapValue(array $attributes) : void
    {
        if (count($attributes) > 0) {
            $this->field->morph->map()->updateOrCreate([], [
                'lat' => $attributes[0]['lat'],
                'long' => $attributes[0]['long']
            ]);
        }
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    protected function deleteMapValue(array $attributes) : void
    {
        if (count($attributes) === 0) {
            $this->field->morph->map()->delete();
        }
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    protected function createRegionsValue(array $attributes) : void
    {
        $this->field->morph->regions()->attach($attributes ?? []);
    }

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    protected function updateRegionsValue(array $attributes) : void
    {
        $this->field->morph->regions()->sync($attributes ?? []);
    }

    /**
     * [updateValues description]
     * @param  array $attributes [description]
     * @return int               [description]
     */
    public function updateValues(array $attributes) : int
    {
        $i = 0;

        foreach ($this->field->morph->group->fields()->get() as $field) {
            if ($field->type === 'image') {
                $path = optional($this->field->morph->fields->where('id', $field->id)
                    ->first())->decode_value;
            }

            if (isset($attributes[$field->id]) && !empty($attributes[$field->id])) {
                if ($field->type === 'regions') {
                    $this->updateRegionsValue($attributes[$field->id]);
                }
    
                if ($field->type === 'map') {
                    $this->updateOrCreateMapValue($attributes[$field->id]);
                }

                $value = $attributes[$field->id];

                if ($value instanceof UploadedFile) {
                    $file = $this->app->make(FileUtil::class, [
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
                if ($field->type === 'map') {
                    $this->deleteMapValue($attributes[$field->id]);
                }

                if ($field->type === 'image') {
                    $this->storage->disk('public')->delete($path);
                }
            }
        }

        $this->field->morph->fields()->sync($ids ?? []);

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
        $this->field->options = array_merge(
            $attributes[$attributes['type']],
            ['required' => $attributes['required']]
        );
        $this->field->save();

        $this->field->morphs()->attach($attributes['morphs'] ?? []);

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

        $this->field->options = array_merge(
            ['required' => $attributes['required']],
            isset($attributes['type']) ?
                $attributes[$attributes['type']] : []
        );

        $this->field->morphs()->sync($attributes['morphs'] ?? []);

        return $this->field->save();
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
}
