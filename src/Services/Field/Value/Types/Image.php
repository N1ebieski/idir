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

namespace N1ebieski\IDir\Services\Field\Value\Types;

use Illuminate\Http\UploadedFile;
use N1ebieski\ICore\Utils\File\File;
use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\Services\Field\Value\Types\Interfaces\FileInterface;

class Image extends Value implements FileInterface
{
    /**
     * Undocumented function
     *
     * @param Field $field
     * @param DB $db
     */
    public function __construct(
        Field $field,
        DB $db,
        protected File $file
    ) {
        parent::__construct($field, $db);

        $this->file = $file;
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile|string $value
     * @return string
     */
    public function prepare($value): string
    {
        if ($value instanceof UploadedFile) {
            // @phpstan-ignore-next-line
            return $this->file->makeFromFile($value)->prepare([
                is_int($this->field->morph->id) ? $this->path() : null
            ]);
        }

        return $value;
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile $value
     * @return string
     */
    public function create(UploadedFile $value): string
    {
        return $this->file->makeFromPath($this->prepare($value))->moveFromTemp($this->path());
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile $value
     * @return string
     */
    public function update(UploadedFile $value): string
    {
        if ($this->getFieldValue() !== $this->path() . "/" . $value->getClientOriginalName()) {
            $this->delete();

            return $this->create($value);
        }

        return $this->getFieldValue();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool
    {
        if ($this->getFieldValue() !== null) {
            return $this->file->delete($this->getFieldValue());
        }

        return false;
    }

    /**
     * Undocumented function
     *
     * @return string|null
     */
    protected function getFieldValue(): ?string
    {
        return optional($this->field->morph->fields->firstWhere('id', $this->field->id))->decode_value;
    }

    /**
     * [path description]
     * @return string     [description]
     */
    protected function path(): string
    {
        return $this->field->path . "/" . $this->field->id . "/" . $this->field->poli . "/" . $this->field->morph->id;
    }
}
