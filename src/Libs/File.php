<?php

namespace N1ebieski\IDir\Libs;

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
    protected $path;

    /**
     * [protected description]
     * @var string
     */
    protected $temp_path = 'vendor/idir/temp';

    /**
     * [private description]
     * @var Storage
     */
    protected $storage;

    /**
     * [__construct description]
     * @param Storage $storage [description]
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param UploadedFile $file
     *
     * @return static
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return static
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * [getFilePath description]
     * @return string [description]
     */
    public function getFilePath() : string
    {
        return $this->path . "/" . $this->file->getClientOriginalName();
    }

    /**
     * [getFileTempPath description]
     * @return string [description]
     */
    public function getFileTempPath() : string
    {
        return $this->temp_path . "/" . $this->file->getClientOriginalName();
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
        return $this->storage->disk('public')->putFile($this->path, $this->file);
    }

    /**
     * [uploadFile description]
     * @return string [description]
     */
    public function uploadToTemp() : string
    {
        return $this->storage->disk('public')->putFile($this->temp_path, $this->file);
    }
}
