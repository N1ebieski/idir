<?php

namespace N1ebieski\IDir\Services\Field\Value\Types\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileInterface
{
    /**
     * Undocumented function
     *
     * @param UploadedFile|string $value
     * @return string
     */
    public function prepare($value): string;

    /**
     * Undocumented function
     *
     * @param UploadedFile $value
     * @return string
     */
    public function create(UploadedFile $value): string;

    /**
     * Undocumented function
     *
     * @param UploadedFile $value
     * @return string
     */
    public function update(UploadedFile $value): string;

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function delete(): bool;
}
