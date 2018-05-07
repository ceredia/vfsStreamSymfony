<?php
/**
 * This file is part of vfsStreamSymfony.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  ceredia\vfsStreamSymfony
 */

namespace ceredia\vfsStreamSymfony\Tests;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStreamDirectory;

use ceredia\vfsStreamSymfony\vfsStream;
use ceredia\vfsStreamSymfony\vfsUploadedFile;
use ceredia\vfsStreamSymfony\vfsHelperTrait;

/**
 * vfsUploadedFileTest
 *
 * @author schrage
 */
class vfsUploadedFileTest extends TestCase
{
    use vfsHelperTrait;

    /** @var vfsStreamDirectory */
    private $root;

    /** @var vfsStreamDirectory */
    private $tmpDir;

    protected function setUp()
    {
        $this->root = vfsStream::setup('root');
    }

    /**
     * @test
     */
    public function construct()
    {
        $dir = __DIR__ . '/../Fixture';
        $file = new vfsUploadedFile($dir . '/file.txt', 'file.txt');
        $vDir = vfsStream::copyPathFromFilesystem(self::getAbsolutePath($dir));

        self::assertTrue($vDir->hasChild('file.txt'));
        self::assertEquals($file->getVfsStreamFile(), $vDir->getChild('file.txt'));
        self::assertEquals($vDir, $file->getParent());
    }
}
