<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Database\Seeders\PHPLD;

use Exception;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\Models\Field\Group\Field;
use N1ebieski\IDir\ValueObjects\Field\Options;
use N1ebieski\IDir\ValueObjects\Field\Visible;
use N1ebieski\IDir\ValueObjects\Field\Required;
use N1ebieski\IDir\Database\Seeders\PHPLD\PHPLDSeeder;

class FieldsSeeder extends PHPLDSeeder
{
    /**
     * Run the database Seeders.
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

                    $field = new Field();

                    $field->id = $this->fieldLastId + $item->ID;
                    $field->title = $item->NAME;
                    $field->desc = strlen($item->DESCRIPTION) > 0 ?
                        strip_tags($item->DESCRIPTION)
                        : null;
                    $field->type = $this->getType($item->TYPE);
                    $field->visible = $item->STATUS === 0 ?
                        Visible::inactive()
                        : Visible::active();
                    $field->options = $this->getOptions($item);

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
    protected function getFieldLastId(): int
    {
        return Field::orderBy('id', 'desc')->first()->id ?? 0;
    }

    /**
     *
     * @param string $type
     * @return Type
     * @throws InvalidArgumentException
     */
    protected function getType(string $type): Type
    {
        return new Type(match ($type) {
            'STR' => Type::INPUT,
            'TXT' => Type::TEXTAREA,
            'DROPDOWN' => Type::SELECT,
            'IMAGEGROUP' => Type::IMAGE,

            default => throw new \InvalidArgumentException("The type '{$type}' not found")
        });
    }

    /**
     *
     * @param mixed $item
     * @return Options
     */
    protected function getOptions($item): Options
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

        $options['required'] = Required::INACTIVE;

        if ($item->TYPE === 'IMAGEGROUP') {
            $options['width'] = 720;
            $options['height'] = 480;
            $options['size'] = 2048;
        }

        return new Options((object)$options);
    }
}
