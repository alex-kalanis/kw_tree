<?php

namespace TraitsTests;


use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_tree\Traits\TFilesFiles;


class FilesFilesTest extends \CommonTestClass
{
    /**
     * @param Node $node
     * @param bool $is
     * @dataProvider callbackProvider
     */
    public function testBasic(Node $node, bool $is): void
    {
        $lib = new XFilesFiles();
        $this->assertEquals($is, $lib->filesExtCallback($node));
    }

    public function callbackProvider(): array
    {
        return [
            [(new Node())->setData(['everytime'], 0, ITypes::TYPE_DIR), true],
            [(new Node())->setData(['nope'], 0, ITypes::TYPE_FILE), false],
            [(new Node())->setData(['nope.txt'], 0, ITypes::TYPE_LINK), false],
            [(new Node())->setData(['nope.doc'], 0, ITypes::TYPE_UNKNOWN), false],
            [(new Node())->setData(['yep.txt'], 0, ITypes::TYPE_FILE), true],
        ];
    }
}


class XFilesFiles
{
    use TFilesFiles;

    public function whichExtsIWant(): array
    {
        return ['txt', 'doc'];
    }
}
