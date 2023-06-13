<?php

namespace TraitsTests;


use kalanis\kw_tree\Traits\TVolumeFile;
use SplFileInfo;


class VolumeFileTest extends \CommonTestClass
{
    /**
     * @param SplFileInfo $node
     * @param bool $is
     * @dataProvider callbackProvider
     */
    public function testBasic(SplFileInfo $node, bool $is): void
    {
        $lib = new XVolumeFile();
        $this->assertEquals($is, $lib->justFilesCallback($node));
    }

    public function callbackProvider(): array
    {
        return [
            [new SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'sub'), false],
            [new SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'dummy2.txt'), true],
        ];
    }
}


class XVolumeFile
{
    use TVolumeFile;
}
