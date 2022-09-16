<?php

namespace AdaptersTests;


use kalanis\kw_tree\Adapters\ArrayAdapter;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;


class ArrayTest extends \CommonTestClass
{
    /**
     * @param string $name
     * @param string $dir
     * @param string $path
     * @param int $size
     * @param string $type
     * @param bool $read
     * @param bool $write
     * @dataProvider dataProvider
     */
    public function testAdapter(string $name, string $dir, string $path, int $size, string $type, bool $read, bool $write): void
    {
        $node = new FileNode();
        $node->setData($path, $dir, $name, $size, $type, $read, $write);
        $sub = new FileNode();
        $sub->setData('ab', 'cd', 'ef', 32, 'gh', false, false);
        $node->addSubNode($sub);
        $lib = new ArrayAdapter();
        $packed = $lib->pack($node);
        $copy = $lib->unpack($packed);
        $this->assertEquals($name, $copy->getName());
        $this->assertEquals($dir, $copy->getDir());
        $this->assertEquals($path, $copy->getPath());
        $this->assertEquals($size, $copy->getSize());
        $this->assertEquals($type, $copy->getType());
        $this->assertEquals($read, $copy->isReadable());
        $this->assertEquals($write, $copy->isWritable());
    }

    public function dataProvider(): array
    {
        return [
            ['abc', 'def', 'ghi', 123, ITree::TYPE_UNKNOWN, false, false],
        ];
    }
}
