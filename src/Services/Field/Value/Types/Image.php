<?php

namespace N1ebieski\IDir\Services\Field\Value\Types;

use Illuminate\Http\UploadedFile;
use N1ebieski\IDir\Models\Field\Field;
use N1ebieski\ICore\Utils\File\FileUtil;
use Illuminate\Database\DatabaseManager as DB;
use N1ebieski\IDir\Services\Field\Value\Types\Interfaces\FileInterface;

class Image extends Value implements FileInterface
{
    /**
     * Undocumented variable
     *
     * @var FileUtil
     */
    protected $fileUtil;

    /**
     * Undocumented function
     *
     * @param Field $field
     * @param DB $db
     */
    public function __construct(Field $field, DB $db, FileUtil $fileUtil)
    {
        parent::__construct($field, $db);

        $this->fileUtil = $fileUtil;
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
            return $this->fileUtil->makeFromFile($value)->prepare([
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
        return $this->fileUtil->makeFromPath($this->prepare($value))->moveFromTemp($this->path());
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
            return $this->fileUtil->delete($this->getFieldValue());
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
