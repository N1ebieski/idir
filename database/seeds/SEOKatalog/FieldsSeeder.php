<?php

namespace N1ebieski\IDir\Seeds\SEOKatalog;

use N1ebieski\IDir\Seeds\SEOKatalogSeeder;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Support\Facades\DB;

class FieldsSeeder extends SEOKatalogSeeder
{
    /**
     * Undocumented function
     *
     * @return integer
     */
    protected static function makeFieldLastId() : int
    {
        return Field::orderBy('id', 'desc')->first()->id ?? 0;        
    }

    /**
     * Undocumented function
     *
     * @param integer $type
     * @return string
     */
    protected static function makeType(int $type) : string
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
    protected static function makeOptions(object $item) : array
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
            $options['width'] = $item->size;
        }        

        return $options;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = DB::connection('import')->table('forms')
            ->orderBy('position', 'asc')->orderBy('title', 'asc')->get();

        $fields->each(function($item) {
            $field = Field::create([
                'id' => $this->field_last_id + $item->id,
                'title' => $item->title,
                'desc' => strlen($item->description) > 0 ? $item->description : null,
                'type' => $this->makeType($item->type),
                'visible' => $item->type_f === 0 ? 1 : 0,
                'options' => $this->makeOptions($item),
            ]);

            $field->morphs()->attach(
                $item->groups === 'all' ?
                $field->morphs()->make()->get('id')->pluck('id')->toArray() :
                collect(array_filter(explode(',', $item->groups)))
                    ->map(function($item) {
                        return $this->group_last_id + $item; 
                    })
            );
        });
    }
}
