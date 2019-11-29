<?php

namespace N1ebieski\IDir\Http\Requests\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use N1ebieski\IDir\Models\Field\Field;

/**
 * [trait description]
 */
trait FieldsExtended
{
    /**
     * [prepareFieldsAttribute description]
     */
    protected function prepareFieldsAttribute() : void
    {
        if (!$this->has('field') && !is_array($this->input('field'))) {
            return;
        }

        foreach ($this->getFields() as $field) {
            $this->prepareFieldImageAttribute($field);
        }
    }

    /**
     * [prepareFieldImageAttribute description]
     * @param Field $field [description]
     */
    protected function prepareFieldImageAttribute(Field $field) : void
    {
        if ($field->type !== 'image') {
            return;
        }

        if (!$this->has("field.{$field->id}") || !is_string($this->input("field.{$field->id}"))) {
            return;
        }

        if (Storage::disk('public')->exists($this->input("field.{$field->id}"))) {
            $this->merge([
                'field' => [
                    $field->id => new UploadedFile(
                        public_path('storage/') . $this->input("field.{$field->id}"),
                        $this->input("field.{$field->id}"),
                        null,
                        null,
                        true
                    )
                ] + $this->input('field')
            ]);
        }
    }

    /**
     * [prepareFieldsRules description]
     * @return array [description]
     */
    protected function prepareFieldsRules() : array
    {
        foreach ($this->getFields() as $field) {
            $rules["field.{$field->id}"][] = 'bail';
            $rules["field.{$field->id}"][] = (bool)$field->options->required === true ?
                'required' : 'nullable';

            switch ($field->type) {
                case 'multiselect' :
                case 'checkbox' :
                    $rules["field.{$field->id}"][] = 'array';
                    break;

                case 'image' :
                    $rules["field.{$field->id}"][] = 'image';
                    $rules["field.{$field->id}"][] = 'mimes:jpeg,png,jpg';
                    $rules["field.{$field->id}"][] = 'max:' . $field->options->size;
                    $rules["field.{$field->id}"][] = 'dimensions:max_width=' . $field->options->width . ',max_height=' . $field->options->height;
                    break;

                default :
                    $rules["field.{$field->id}"][] = 'string';
            }

            if (isset($field->options->options)) {
                $rules["field.{$field->id}"][] = 'in:' . implode(',', $field->options->options);
            }
            if (isset($field->options->min)) {
                $rules["field.{$field->id}"][] = 'min:' . $field->options->min;
            }
            if (isset($field->options->max)) {
                $rules["field.{$field->id}"][] = 'max:' . $field->options->max;
            }
        }

        return $rules ?? [];
    }
}
