<?php

namespace N1ebieski\IDir\Http\Requests\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Support\Facades\Storage;
use N1ebieski\IDir\ValueObjects\Field\Type;
use Illuminate\Support\Collection as Collect;

trait FieldsExtended
{
    /**
     * [prepareFieldsAttribute description]
     */
    protected function prepareFieldsAttribute(): void
    {
        if (!$this->has('field') && !is_array($this->input('field'))) {
            return;
        }

        foreach ($this->getFields() as $field) {
            $this->prepareFieldMapAttribute($field);
            $this->prepareFieldImageAttribute($field);
        }
    }

    /**
     * Undocumented function
     *
     * @param Field $field
     * @return void
     */
    protected function prepareFieldMapAttribute(Field $field): void
    {
        if (!$field->type->isMap()) {
            return;
        }

        if (!$this->has("field.{$field->id}") || !is_array($this->input("field.{$field->id}"))) {
            return;
        }

        $this->merge([
            'field' => [
                $field->id => Collect::make($this->input("field.{$field->id}"))
                    ->filter(function ($item) {
                        return isset($item['lat']) && $item['lat'] !== null
                            && isset($item['long']) && $item['long'] !== null;
                    })
                    ->toArray()
            ] + $this->input('field')
        ]);
    }

    /**
     * [prepareFieldImageAttribute description]
     * @param Field $field [description]
     */
    protected function prepareFieldImageAttribute(Field $field): void
    {
        if (!$field->type->isImage()) {
            return;
        }

        if ($this->missing("field.{$field->id}") && $this->has("delete_img.{$field->id}")) {
            $this->merge([
                'field' => [
                    $field->id => null
                ] + $this->input('field')
            ]);
        }

        if ($this->missing("field.{$field->id}") || !is_string($this->input("field.{$field->id}"))) {
            return;
        }

        if (Storage::disk('public')->exists($this->input("field.{$field->id}"))) {
            $path = public_path('storage/') . $this->input("field.{$field->id}");

            $this->merge([
                'field' => [
                    $field->id => new UploadedFile(
                        $path,
                        $this->input("field.{$field->id}"),
                        mime_content_type($path),
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
    protected function prepareFieldsRules(): array
    {
        foreach ($this->getFields() as $field) {
            $rules["field.{$field->id}"][] = 'bail';
            $rules["field.{$field->id}"][] = $field->options->required->isActive() ? 'required' : 'nullable';

            switch ($field->type) {
                case Type::MAP:
                    $rules["field.{$field->id}"][] = 'array';
                    $rules["field.{$field->id}"][] = 'max:1';
                    $rules["field.{$field->id}"][] = 'no_js_validation';
                    $rules["field.{$field->id}.*.lat"] = [
                        'bail',
                        $field->options->required->isActive() ? 'required' : 'nullable',
                        "required_with:field.{$field->id}.*.long",
                        'numeric',
                        'between:-90,90'
                    ];
                    $rules["field.{$field->id}.*.long"] = [
                        'bail',
                        $field->options->required->isActive() ? 'required' : 'nullable',
                        "required_with:field.{$field->id}.*.lat",
                        'numeric',
                        'between:-180,180'
                    ];
                    break;

                case Type::REGIONS:
                    $rules["field.{$field->id}"][] = 'array';
                    $rules["field.{$field->id}"][] = 'exists:regions,id';
                    break;

                case Type::GUS:
                    $rules["field.{$field->id}"][] = 'not_present';
                    $rules["field.{$field->id}"][] = 'no_js_validation';
                    break;

                case Type::MULTISELECT:
                case Type::CHECKBOX:
                    $rules["field.{$field->id}"][] = 'array';
                    break;

                case Type::IMAGE:
                    $rules["field.{$field->id}"][] = 'image';
                    $rules["field.{$field->id}"][] = 'mimes:jpeg,png,jpg';
                    $rules["field.{$field->id}"][] = 'max:' . $field->options->size;
                    $rules["field.{$field->id}"][] = 'dimensions:max_width=' . $field->options->width . ',max_height=' . $field->options->height;
                    break;

                case Type::INPUT:
                case Type::TEXTAREA:
                    $rules["field.{$field->id}"][] = 'string';
            }

            if (isset($field->options->options)) {
                $rules["field.{$field->id}"][] = Rule::in($field->options->options);
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
