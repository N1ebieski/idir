<?php

namespace N1ebieski\IDir\Services\Field\Value\Types;

use Illuminate\Http\UploadedFile;
use N1ebieski\ICore\Utils\FileUtil;
use N1ebieski\IDir\Models\Field\Field;
use Illuminate\Database\DatabaseManager as DB;

class Image extends Value
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
     * @return string|null
     */
    public function getFieldValue(): ?string
    {
        return optional($this->field->morph->fields->where('id', $this->field->id)->first())->decode_value;
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
            $path = is_int($this->field->morph->id) ? $this->path() : null;

            return $this->fileUtil->make($value, $path)->prepare();
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
        $file = $this->fileUtil->make($value, $this->path());

        $file->prepare();
        $file->moveFromTemp();

        return $file->getFilePath();
    }

    /**
     * Undocumented function
     *
     * @param UploadedFile $value
     * @return string
     */
    public function update(UploadedFile $value): string
    {
        $file = $this->fileUtil->make($value, $this->path());

        if ($this->getFieldValue() !== $file->getFilePath()) {
            $file->prepare();
            $file->moveFromTemp();

            $this->delete();
        }

        return $file->getFilePath();
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool
    {
        return $this->fileUtil->make(null, $this->getFieldValue())->delete();
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
