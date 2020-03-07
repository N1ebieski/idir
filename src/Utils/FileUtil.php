<?php

namespace N1ebieski\IDir\Utils;

use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Factory as Storage;

/**
 * [File description]
 */
class FileUtil
{
    /**
     * [$file description]
     * @var UploadedFile
     */
    protected $file;

    /**
     * [protected description]
     * @var string
     */
    protected string $file_path;

    /**
     * [protected description]
     * @var string
     */
    protected string $file_temp_path;

    /**
     * [protected description]
     * @var string|null
     */
    protected $path;

    /**
     * [protected description]
     * @var string
     */
    protected string $temp_path = 'vendor/idir/temp';

    /**
     * [private description]
     * @var Storage
     */
    protected $storage;

    /**
     * [__construct description]
     * @param Storage      $storage [description]
     * @param UploadedFile $file    [description]
     * @param string|null  $path    [description]
     */
    public function __construct(Storage $storage, UploadedFile $file, string $path = null)
    {
        $this->storage = $storage;

        $this->file = $file;
        $this->path = $path;

        $this->makeFileTempPath();
        $this->makeFilePath();
    }

    /**
     * @return string
     */
    public function getFilePath() : string
    {
        return $this->file_path;
    }

    /**
     * @return string
     */
    public function getFileTempPath() : string
    {
        return $this->file_temp_path;
    }

    /**
     * [getFileTempPath description]
     * @return string [description]
     */
    protected function makeFileTempPath() : string
    {
        return $this->file_temp_path = $this->temp_path . "/" . $this->file->getClientOriginalName();
    }

    /**
     * [getFilePath description]
     * @return string [description]
     */
    protected function makeFilePath() : string
    {
        return $this->file_path = $this->path . "/" . $this->file->getClientOriginalName();
    }

    /**
     * [prepareFile description]
     * @return string [description]
     */
    public function prepare() : string
    {
        foreach ([$this->getFilePath(), $this->getFileTempPath()] as $path) {
            if ($this->storage->disk('public')->exists($path)) {
                return $path;
            }
        }

        return $this->uploadToTemp();
    }

    /**
     * [move description]
     * @return bool [description]
     */
    public function moveFromTemp() : bool
    {
        return $this->storage->disk('public')
            ->move($this->getFileTempPath(), $this->getFilePath());
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function upload() : string
    {
        $this->file_path = $this->storage->disk('public')
            ->putFile($this->path, $this->file);

        return $this->getFilePath();
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function uploadToTemp() : string
    {
        $this->file_temp_path = $this->storage->disk('public')
            ->putFile($this->temp_path, $this->file);

        $this->file_path = $this->path . "/" . basename($this->getFileTempPath());

        return $this->getFileTempPath();
    }
}
