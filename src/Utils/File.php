<?php

namespace N1ebieski\IDir\Utils;

use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Factory as Storage;

/**
 * [File description]
 */
class File
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

        $this->setFileTempPath($this->makeFileTempPath());
        $this->setFilePath($this->makeFilePath());
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
     * [setFileTempPath description]
     * @param  string $path [description]
     * @return self         [description]
     */
    public function setFileTempPath(string $path) : self
    {
        $this->file_temp_path = $path;

        return $this;
    }

    /**
     * [setFilePath description]
     * @param  string $path [description]
     * @return self         [description]
     */
    public function setFilePath(string $path) : self
    {
        $this->file_path = $path;

        return $this;
    }

    /**
     * [getFileTempPath description]
     * @return string [description]
     */
    protected function makeFileTempPath() : string
    {
        return $this->temp_path . "/" . $this->file->getClientOriginalName();
    }

    /**
     * [getFilePath description]
     * @return string [description]
     */
    protected function makeFilePath() : string
    {
        return $this->path . "/" . $this->file->getClientOriginalName();
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
        return $this->storage->disk('public')->move($this->getFileTempPath(), $this->getFilePath());
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function upload() : string
    {
        $this->setFilePath(
            $this->storage->disk('public')->putFile($this->path, $this->file)
        );

        return $this->getFilePath();
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function uploadToTemp() : string
    {
        $this->setFileTempPath(
            $this->storage->disk('public')->putFile($this->temp_path, $this->file)
        );

        $this->setFilePath(
            $this->path . "/" . basename($this->getFileTempPath())
        );

        return $this->getFileTempPath();
    }
}
