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

namespace N1ebieski\IDir\Testing\Traits\Field;

use N1ebieski\IDir\Models\Group;
use Illuminate\Http\UploadedFile;
use N1ebieski\IDir\ValueObjects\Field\Type;
use N1ebieski\IDir\Models\Field\Group\Field;

trait HasFields
{
    /**
     * [setUpFields description]
     * @param  Group $group [description]
     * @return array        [description]
     */
    private function setUpFields(Group $group): array
    {
        $fields = [];

        foreach (Type::getAvailable() as $type) {
            /** @var Field */
            $field = Field::makeFactory()->public()->hasAttached($group, [], 'morphs')->{$type}()->create();

            $fields['field'][$field->id] = match ($field->type->getValue()) {
                Type::INPUT, Type::TEXTAREA => 'Cupidatat magna enim officia non sunt esse qui Lorem quis.',

                // @phpstan-ignore-next-line
                Type::SELECT => $field->options->options[0],

                // @phpstan-ignore-next-line
                Type::MULTISELECT, Type::CHECKBOX => array_slice($field->options->options, 0, 2),

                Type::SWITCH => '1',

                Type::IMAGE => UploadedFile::fake()->image('avatar.jpg', 500, 200)->size(1000),

                default => throw new \InvalidArgumentException("The Type '{$field->type}' not found")
            };
        }

        return $fields;
    }
}
