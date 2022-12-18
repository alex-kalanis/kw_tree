<?php

namespace AdaptersTests;


use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_tree\Adapters\ArrayAdapter;
use kalanis\kw_tree\Essentials\FileNode;


class ArrayTest extends \CommonTestClass
{
    /**
     * @param string[] $path
     * @param int $size
     * @param string $type
     * @param bool $read
     * @param bool $write
     * @dataProvider dataProvider
     */
    public function testAdapter(array $path, int $size, string $type, bool $read, bool $write): void
    {
        $node = new FileNode();
        $node->setData($path, $size, $type, $read, $write);
        $sub = new FileNode();
        $sub->setData(['ab', 'cd', 'ef'], 32, 'gh', false, false);
        $node->addSubNode($sub);
        $lib = new ArrayAdapter();
        $packed = $lib->pack($node);
        $copy = $lib->unpack($packed);
        $this->assertEquals($path, $copy->getPath());
        $this->assertEquals($size, $copy->getSize());
        $this->assertEquals($type, $copy->getType());
        $this->assertEquals($read, $copy->isReadable());
        $this->assertEquals($write, $copy->isWritable());
    }

    public function dataProvider(): array
    {
        return [
            [['abc', 'def', 'ghi'], 123, ITypes::TYPE_UNKNOWN, false, false],
        ];
    }
}
