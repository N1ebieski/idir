<?php

namespace N1ebieski\IDir\Seeds\PHPLD;

use N1ebieski\IDir\Seeds\PHPLD\PHPLDSeeder;
use N1ebieski\IDir\Models\Field\Group\Field;
use Illuminate\Support\Facades\DB;

class FieldsSeeder extends PHPLDSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = DB::connection('import')->table('submit_item_status')
            ->get();

        DB::connection('import')->table('submit_item')
            ->leftJoin('submit_item_value', 'submit_item.ID', '=', 'submit_item_value.ITEM_ID')
            ->orderBy('ORDER_ID', 'asc')
            ->orderBy('ID', 'asc')
            ->get()
            ->each(function ($item) use ($groups) {
                DB::transaction(function () use ($item, $groups) {
                    if ($item->IS_DEFAULT === 1) {
                        return;
                    }

                    $field = Field::make();

                    $field->id = $this->fieldLastId + $item->ID;
                    $field->title = $item->NAME;
                    $field->desc = strlen($item->DESCRIPTION) > 0 ?
                        strip_tags($item->DESCRIPTION)
                        : null;
                    $field->type = $this->type($item->TYPE);
                    $field->visible = $item->STATUS === 0 ?
                        Field::INVISIBLE
                        : FIELD::VISIBLE;
                    $field->options = $this->options($item);

                    $field->save();

                    $field->morphs()->attach(
                        collect($groups->where('ITEM_ID', $item->ID))
                            ->map(function ($item) {
                                return $this->groupLastId + $item->LINK_TYPE_ID;
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
     * @param string $type
     * @return string
     */
    protected static function type(string $type) : string
    {
        switch ($type) {
            case 'STR':
                return 'input';

            case 'TXT':
                return 'textarea';

            case 'DROPDOWN':
                return 'select';

            case 'IMAGEGROUP':
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
        if ($item->TYPE === 'STR') {
            $options['min'] = 3;
            $options['max'] = 255;
        }

        if ($item->TYPE === 'TXT') {
            $options['min'] = 3;
            $options['max'] = 5000;
        }

        if ($item->TYPE === 'DROPDOWN' && !empty($item->VALUE)) {
            $value = array_filter(explode(",", $item->VALUE));
            array_shift($value);

            $options['options'] = $value;
        }

        $options['required'] = Field::OPTIONAL;

        if ($item->TYPE === 'IMAGEGROUP') {
            $options['width'] = 720;
            $options['height'] = 480;
            $options['size'] = 2048;
        }

        return $options;
    }
}
