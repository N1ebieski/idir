<?php

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\Models\Field\Group\Field;
use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\IDir\Database\Seeders\SEOKatalog\SEOKatalogSeeder;

class FieldsSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database Seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('import')->table('forms')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc')
            ->get()
            ->each(function ($item) {
                DB::transaction(function () use ($item) {
                    if ($item->mod !== 0) {
                        return;
                    }

                    $field = Field::make();

                    $field->id = $this->fieldLastId + $item->id;
                    $field->title = $item->title;
                    $field->desc = strlen($item->description) > 0 ?
                        $item->description
                        : null;
                    $field->type = $this->type($item->type);
                    $field->visible = $item->type_f === 0 ?
                        Visible::ACTIVE
                        : Visible::INACTIVE;
                    $field->options = $this->options($item);

                    $field->save();

                    $field->morphs()->attach(
                        $item->groups === 'all' ?
                        $field->morphs()->make()->get('id')->pluck('id')->toArray()
                        : collect(array_filter(explode(',', $item->groups)))
                            ->map(function ($item) {
                                return $this->groupLastId + $item;
                            })
                    );
                });
            });
    }

    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function fieldLastId(): int
    {
        return Field::orderBy('id', 'desc')->first()->id ?? 0;
    }

    /**
     * Undocumented function
     *
     * @param integer $type
     * @return string
     */
    protected static function type(int $type): string
    {
        switch ($type) {
            case 1:
                return Type::INPUT;

            case 2:
                return Type::TEXTAREA;

            case 3:
                return Type::SELECT;

            case 4:
                return Type::CHECKBOX;

            case 5:
                return Type::IMAGE;
        }
    }

    /**
     * Undocumented function
     *
     * @param object $item
     * @return array
     */
    protected static function options(object $item): array
    {
        if ($item->min >= 0) {
            $options['min'] = $item->min;
        }

        if ($item->max > 0) {
            $options['max'] = $item->max;
        }

        if (!empty($item->options)) {
            $options['options'] = array_filter(explode("<br />", $item->options));
        }

        $options['required'] = $item->must;

        if ($item->width > 0) {
            $options['width'] = $item->width;
        }

        if ($item->height > 0) {
            $options['height'] = $item->height;
        }

        if ($item->size > 0) {
            $options['size'] = $item->size;
        }

        return $options;
    }
}