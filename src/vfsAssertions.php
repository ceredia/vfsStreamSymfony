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
        $filename = self::extractFilename($path);
        $dir = vfsStream::getExistingDirectory($path);
        self::assertThat(
            $dir->hasChild($filename),
            self::isTrue(),
            'The file "' . $path . '" does not exist.'
        );
    }

    /**
     * @param vfsStreamFile $vfsFile
     */
    protected static function assertVirtualFileDeleted($vfsFile)
    {
        self::assertEquals(
            vfsStream::SCHEME . '://' . $vfsFile->getName(),
            $vfsFile->url(),
            'The file "' . $vfsFile->url() . '" has not been deleted.'
        );
    }
}