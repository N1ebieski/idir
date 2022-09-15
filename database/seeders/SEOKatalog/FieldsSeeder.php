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

namespace N1ebieski\IDir\Database\Seeders\SEOKatalog;

use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\Models\Field\Group\Field;
use N1ebieski\IDir\ValueObjects\Field\Options;
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

                    $field = new Field();

                    $field->id = $this->fieldLastId + $item->id;
                    $field->title = $item->title;
                    $field->desc = strlen($item->description) > 0 ?
                        $item->description
                        : null;
                    $field->type = $this->getType($item->type);
                    $field->visible = $item->type_f === 0 ?
                        Visible::active()
                        : Visible::inactive();
                    $field->options = $this->getOptions($item);

                    $field->save();

                    $field->morphs()->attach(
                        $item->groups === 'all' ?
                        $field->morphs()->make()->pluck('id')->toArray()
                        : collect(array_filter(explode(',', $item->groups)))
                            ->map(function ($item) {
                                return $this->groupLastId + (int)$item;
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
     * @param int $type
     * @return Type
     * @throws InvalidArgumentException
     */
    protected function getType(int $type): Type
    {
        return new Type(match ($type) {
            1 => Type::INPUT,
            2 => Type::TEXTAREA,
            3 => Type::SELECT,
            4 => Type::CHECKBOX,
            5 => Type::IMAGE,

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

        return new Options((object)$options);
    }
}
