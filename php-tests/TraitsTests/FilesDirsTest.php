<?php

namespace TraitsTests;


use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_tree\Traits\TFilesDirs;


class FilesDirsTest extends \CommonTestClass
{
    /**
     * @param Node $node
     * @param bool $is
     * @dataProvider callbackProvider
     */
    public function testBasic(Node $node, bool $is): void
    {
        $lib = new XFilesDirs();
        $this->assertEquals($is, $lib->justDirsCallback($node));
    }

    public function callbackProvider(): array
    {
        return [
            [(new Node())->setData([], 0, ITypes::TYPE_DIR), true],
            [(new Node())->setData([], 0, ITypes::TYPE_FILE), false],
            [(new Node())->setData([], 0, ITypes::TYPE_LINK), false],
            [(new Node())->setData([], 0, ITypes::TYPE_BLOCK), false],
            [(new Node())->setData([], 0, ITypes::TYPE_FIFO), false],
            [(new Node())->setData([], 0, ITypes::TYPE_CHAR), false],
            [(new Node())->setData([], 0, ITypes::TYPE_SOCKET), false],
            [(new Node())->setData([], 0, ITypes::TYPE_UNKNOWN), false],
        ];
    }
}


class XFilesDirs
{
    use TFilesDirs;
}
