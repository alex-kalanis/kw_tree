<?php

namespace TraitsTests;


use kalanis\kw_tree\Traits\TVolumeFiles;
use SplFileInfo;


class VolumeFilesTest extends \CommonTestClass
{
    /**
     * @param SplFileInfo $node
     * @param bool $is
     * @dataProvider callbackProvider
     */
    public function testBasic(SplFileInfo $node, bool $is): void
    {
        $lib = new XVolumeFiles();
        $this->assertEquals($is, $lib->filesExtCallback($node));
    }

    public function callbackProvider(): array
    {
        return [
            [new SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'sub'), true],
            [new SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'last_one' . DIRECTORY_SEPARATOR . '.gitkeep'), false],
            [new SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'dummy2.txt'), true],
        ];
    }
}


class XVolumeFiles
{
    use TVolumeFiles;

    public function whichExtsIWant(): array
    {
        return ['txt', 'doc'];
    }
}
