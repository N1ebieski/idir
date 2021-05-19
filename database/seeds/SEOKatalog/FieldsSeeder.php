<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalog\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Support\Facades\DB;

class FieldsSeeder extends SEOKatalogSeeder
{
    /**
     * Run the database seeds.
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
                        Field::VISIBLE
                        : Field::INVISIBLE;
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
    protected static function fieldLastId() : int
    {
        return Field::orderBy('id', 'desc')->first()->id ?? 0;
    }

    /**
     * Undocumented function
     *
     * @param integer $type
     * @return string
     */
    protected static function type(int $type) : string
    {
        switch ($type) {
            case 1:
                return 'input';

            case 2:
                return 'textarea';

            case 3:
                return 'select';

            case 4:
                return 'checkbox';

            case 5:
                return 'image';
        }
    }

    /**
     * Undocumented function
     *
     * @param object $item
     * @return array
     */
    protected static function options(object $item) : array
    {
        if ($item->min !== 0) {
            $options['min'] = $item->min;
        }

        if ($item->max !== 0) {
            $options['max'] = $item->max;
        }

        if (!empty($item->options)) {
            $options['options'] = array_filter(explode("<br />", $item->options));
        }

        $options['required'] = $item->must;

        if ($item->width !== 0) {
            $options['width'] = $item->width;
        }

        if ($item->height !== 0) {
            $options['height'] = $item->height;
        }
        
        if ($item->size !== 0) {
            $options['size'] = $item->size;
        }

        return $options;
    }
}
