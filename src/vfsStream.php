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

use org\bovigo\vfs\vfsStream as Base;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * vfsStream
 * 
 * @author Robin Schrage
 */
class vfsStream extends Base
{
    use vfsHelperTrait;

    /**
     * Copies the file system path directories from given path into the base dir.
     *
     * If no baseDir is given it will try to add the structure to the existing
     * root directory without replacing existing childs except those with equal
     * names.
     * Permissions are copied as well.
     * Files are not copied at all.
     * 
     * @parameter string $path the path to copy from the file system
     * @parameter bool $overwrite if true, existing virtual directories are replaced with new (empty) ones
     * @return vfsStreamDirectory the virtual directory of the given path
     * @throws \InvalidArgumentException
     */
    public static function copyPathFromFilesystem(
        $path,
        $overwrite = false
    ) {
        $node = vfsStreamWrapper::getRoot();

        if (null === $node) {
            throw new \InvalidArgumentException('No root directory set.');
        }

        $pathDir = self::isAbsolutPath($path) ? DIRECTORY_SEPARATOR : '';
        foreach (self::getAbsoluteDirectories($path) as $dir) {
            $pathDir .= $dir . DIRECTORY_SEPARATOR;
            if (!is_dir($pathDir)) {
                throw new \InvalidArgumentException('The directory "' . $pathDir . '" does not exist.');
            }
            if (!is_readable($pathDir)) {
                throw new \InvalidArgumentException('The directory "' . $pathDir . '" is not readable.');
            }
            if (!$node->hasChild($dir) || ($node->hasChild($dir) && $overwrite)) {
                self::newDirectory(
                    $dir,
                    octdec(substr(sprintf('%o', fileperms($pathDir)), -4))
                )->at($node);
            }
            $node = $node->getChild($dir);
        }
        return $node;
    }

    /**
     * Copies the file system structure (all sub directories) from given path into the given base dir.
     *
     * If no baseDir is given it will try to add the structure to the existing
     * root directory without replacing existing childs except those with equal
     * names.
     * Permissions are copied as well.
     * Files are not copied at all.
     *
     * @parameter string $path the path representing the structure to copy from the file system
     * @parameter vfsStreamDirectory the virtual directory to copy the structure into
     * @return vfsStreamDirectory the virtual directory of the given path
     * @throws \InvalidArgumentException
     */
    public static function copyStructureFromFilesystem(
        string $path,
        vfsStreamDirectory $baseDir = null
    ) {
        if (null === $baseDir) {
            $baseDir = vfsStreamWrapper::getRoot();
        }

        if (null === $baseDir) {
            throw new \InvalidArgumentException('No baseDir given and no root directory set.');
        }

        $dir = new \DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            switch (filetype($fileinfo->getPathname())) {
                case 'file':
                    continue;

                case 'dir':
                    if (!$fileinfo->isDot()) {
                        self::copyStructureFromFileSystem(
                            $fileinfo->getPathname(),
                            self::newDirectory(
                                    $fileinfo->getFilename(),
                                    octdec(substr(sprintf('%o', $fileinfo->getPerms()), -4))
                            )->at($baseDir)
                        );
                    }
                    break;

                case 'block':
                    self::newBlock(
                            $fileinfo->getFilename(),
                            octdec(substr(sprintf('%o', $fileinfo->getPerms()), -4))
                        )->at($baseDir);
                    break;
            }
        }
        return $baseDir;
    }

    /**
     * Returns the virtual directory to the virtual given path.
     * 
     * @param string $path the path to the virtual directory
     * @return vfsStreamDirectory
     * @throws \InvalidArgumentException
     */
    public static function getExistingDirectory($path)
    {
        $node = vfsStreamWrapper::getRoot();

        if (null === $node) {
            throw new \InvalidArgumentException('No root directory set.');
        }
        if (substr($path, 0, strlen($node->url())) !== $node->url()) {
            throw new \InvalidArgumentException('Not a virtual path: "' . $path . '".');
        }

        $relPath = substr($path, strlen($node->url()));

        $nodePath = $node->url();
        foreach (self::getAbsoluteDirectories($relPath) as $dir) {
            if (!$node->hasChild($dir)) {
                throw new \InvalidArgumentException('The virtual directory "' . $nodePath . '/' . $dir . '" does not exist.');
            }
            $node = $node->getChild($dir);
            $nodePath = $node->url();
        }
        return $node;
    }

    /**
     * Returns a new vfsUploadedFile at the given path with given original name.
     *
     * @param   string  $path         the
     * @param   string  $originalName original name of file to create
     * @return  vfsStreamFile
     */
    public static function newUploadedFile(string $path, string $originalName)
    {
        return new vfsUploadedFile($path, $originalName);
    }
}
