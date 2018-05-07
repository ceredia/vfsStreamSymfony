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
use ceredia\vfsStreamSymfony\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

use ceredia\vfsStreamSymfony\vfsHelperTrait;

/**
 * vfsStreamSymfony
 *
 * @author schrage
 */
class vfsStreamTest extends TestCase
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
     * @dataProvider invalidDirectories
     */
    public function copyInvalidPathFromFilesystem($path)
    {
        $this->expectExceptionMessage('does not exist');
        $this->expectException(\InvalidArgumentException::class);
        vfsStream::copyPathFromFilesystem($path);
    }

    public function invalidDirectories()
    {
        return [
            ['nodir'],
            ['./nodir'],
            [__DIR__ . '/nodir'],
            [__DIR__ . '/../Fixture/nodir'],
            ['/' . (string)\uniqid()],
        ];
    }

    /**
     * @test
     * @dataProvider unreadableDirectories
     */
    public function copyUnreadablePathFromFilesystem($path)
    {
        $this->expectExceptionMessage('is not readable');
        $this->expectException(InvalidArgumentException::class);
        vfsStream::copyPathFromFilesystem($path);
    }

    /**
     * TODO
     */
    public function unreadableDirectories()
    {
        return [];
    }

    /**
     * @test
     * @dataProvider directories
     */
    public function copyPathFromFilesystem($path)
    {
        $this->tmpDir = vfsStream::copyPathFromFilesystem($path);
        self::assertEquals($this->rootUrl() . self::getAbsolutePath($path), $this->tmpDir->url());
    }

    /**
     * @test
     * @dataProvider directories
     */
    public function getExistingDirectory($path)
    {
        $this->tmpDir = vfsStream::copyPathFromFilesystem($path);
        self::assertEquals($this->tmpDir, vfsStream::getExistingDirectory($this->rootUrl() . $path));
    }

    /**
     * @test
     * @dataProvider directories
     */
    public function getExistingDirectoryFromInvalidPath($path)
    {
        $this->expectExceptionMessage('Not a virtual path');
        $this->expectException(\InvalidArgumentException::class);
        $this->tmpDir = vfsStream::getExistingDirectory($path);
    }

    
    public function directories()
    {
        return [
            [__DIR__],
            [__DIR__ . '/../Fixture'],
            ['/tmp'],
        ];
    }
}
