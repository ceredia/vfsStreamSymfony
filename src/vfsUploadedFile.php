<?php
/**
 * This file is part of vfsStreamSymfony.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  ceredia\vfsStreamSymfony
 */

namespace ceredia\vfsStreamSymfony;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * vfsUploadedFile
 * 
 * @author Robin Schrage
 */
class vfsUploadedFile extends UploadedFile
{
    /** * @var vfsStreamFile */
    protected $vfsStreamFile;

    /** * @var vfsStreamDirectory */
    protected $parent;

    /** * @var bool */
    protected $makeDirs = false;

    /** * @var string */
    protected $originalName;

    /** * @var string */
    protected $mimeType;

    /** @var int */
    protected $size;

    /** @var int */
    protected $error;

    /** @var string */
    protected $content = 'some content';

    /**
     * 
     * @param string $path
     * @param string $originalName
     * @param string $mimeType
     * @param int $size
     * @param int $error
     * @param bool $test
     */
    public function __construct($path, $originalName, $mimeType = null, $size = null, $error = null, $test = false)
    {
        $this->originalName = $this->getName($originalName);
        $this->mimeType = $mimeType ?: 'application/octet-stream';
        $this->size = $size;
        $this->error = $error ?: UPLOAD_ERR_OK;
        $this->test = true;

        $this->vfsStreamFile = new vfsStreamFile($this->originalName);
        $this->parent = vfsStream::copyPathFromFilesystem($path);
        $this->vfsStreamFile->at($this->parent);
    }

    /**
     * Returns the original file name.
     * 
     * @return string|null The original name
     */
    public function getClientOriginalName()
    {
        return $this->originalName;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return vfsStreamDirectory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * If set to true, a move operation will create the virtual path
     * for the target directory by copying the path from the real filesystem
     * 
     * @param bool $makeDirs
     */
    public function setMakeDirs($makeDirs)
    {
        $this->makeDirs = $makeDirs;
    }

    /**
     * @param string $directory
     * @param string $name
     * @return vfsUploadedFile
     */
    public function move($directory, $name = null)
    {
        $root = vfsStreamWrapper::getRoot();

        if (null === $root) {
            throw new \InvalidArgumentException('No root directory set.');
        }

        if ($name) {
            $this->vfsStreamFile->rename($name);
        }
        $this->parent->removeChild($this->originalName);
        if ($this->makeDirs) {
            $this->parent = vfsStream::copyPathFromFilesystem($directory);
        } else {
            $this->parent = vfsStream::getExistingDirectoryFromPath($directory);
        }
        $this->vfsStreamFile->at($this->parent);
        return $this;
    }

    /**
     * @return vfsStreamFile
     */
    public function getVfsStreamFile()
    {
        return $this->vfsStreamFile;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return true;
    }
}
