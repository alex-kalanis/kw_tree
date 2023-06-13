<?php

namespace TraitsTests;


use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_tree\Traits\TFilesFile;


class FilesFileTest extends \CommonTestClass
{
    /**
     * @param Node $node
     * @param bool $is
     * @dataProvider callbackProvider
     */
    public function testBasic(Node $node, bool $is): void
    {
        $lib = new XFilesFile();
        $this->assertEquals($is, $lib->justFilesCallback($node));
    }

    public function callbackProvider(): array
    {
        return [
            [(new Node())->setData([], 0, ITypes::TYPE_DIR), false],
            [(new Node())->setData([], 0, ITypes::TYPE_FILE), true],
            [(new Node())->setData([], 0, ITypes::TYPE_LINK), false],
            [(new Node())->setData([], 0, ITypes::TYPE_BLOCK), false],
            [(new Node())->setData([], 0, ITypes::TYPE_FIFO), false],
            [(new Node())->setData([], 0, ITypes::TYPE_CHAR), false],
            [(new Node())->setData([], 0, ITypes::TYPE_SOCKET), false],
            [(new Node())->setData([], 0, ITypes::TYPE_UNKNOWN), false],
        ];
    }
}


class XFilesFile
{
    use TFilesFile;
}
