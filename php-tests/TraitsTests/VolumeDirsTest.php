<?php

namespace TraitsTests;


use kalanis\kw_tree\Traits\TVolumeDirs;
use SplFileInfo;


class VolumeDirsTest extends \CommonTestClass
{
    /**
     * @param SplFileInfo $node
     * @param bool $is
     * @dataProvider callbackProvider
     */
    public function testBasic(SplFileInfo $node, bool $is): void
    {
        $lib = new XVolumeDirs();
        $this->assertEquals($is, $lib->justDirsCallback($node));
    }

    public function callbackProvider(): array
    {
        return [
            [new SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'sub'), true],
            [new SplFileInfo(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'dummy2.txt'), false],
        ];
    }
}


class XVolumeDirs
{
    use TVolumeDirs;
}
