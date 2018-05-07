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

use org\bovigo\vfs\vfsStreamWrapper;

/**
 * vfsHelperTrait
 * 
 * @author Robin Schrage
 */
trait vfsHelperTrait
{
    protected static function isAbsolutPath($path)
    {
        return substr($path, 0, 1) === DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $path
     * @param bool $stripFile
     * @return string
     */
    protected static function getAbsolutePath($path, $stripFile = false)
    {
        return DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, self::getAbsolutes($path, $stripFile));
    }

    /**
     * @param string $path
     * @return array
     */
    protected static function getAbsoluteDirectories($path)
    {
        return self::getAbsolutes($path, true);
    }

    /**
     * @param string $path
     * @param bool $stripFile
     * @return array
     */
    protected static function getAbsolutes($path, $stripFile = false)
    {
        $dirPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        if ($stripFile) {
            $dirPath = self::stripFilename($dirPath);
        }
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $dirPath), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return $absolutes;
    }

    /**
     * @param string $path
     * @return string
     */
    protected static function stripFilename($path)
    {
        $parts = pathinfo($path);
        if (isset($parts['extension']) && strlen($parts['extension']) > 0) {
            return $parts['dirname'];
        }
        return $path;
    }

    /**
     * @param string $path
     * @return string|null
     */
    protected static function extractFilename($path)
    {
        return preg_replace('/^.+[\\\\\\/]/', '', $path);
    }

    /**
     * @return string
     */
    protected static function rootUrl()
    {
        return vfsStreamWrapper::getRoot()->url();
    }
}