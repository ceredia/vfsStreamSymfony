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

/**
 * vfsAssertions
 * 
 * @author Robin Schrage
 */
trait vfsAssertions
{
    use vfsHelperTrait;

    /**
     * @param string $path
     */
    protected static function assertVirtualFileExists($path)
    {
        self::assertVirtualFile($path, true);
    }

    /**
     * @param string $path
     */
    protected static function assertVirtualFileNotExists($path)
    {
        self::assertVirtualFile($path, false);
    }

    /**
     * @param string $path
     * @param bool $exists
     */
    private static function assertVirtualFile($path, $exists)
    {
        $filename = self::extractFilename($path);
        $dir = vfsStream::getExistingDirectoryFromPath($path);
        self::assertThat(
            $dir->hasChild($filename),
            $exists ? self::isTrue() : self::isFalse(),
            'The file "' . $path . '" does not exist.'
        );
    }
}