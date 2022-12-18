<?php

namespace BasicTests;


use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_tree\Essentials\FileNode;


class NodeTest extends \CommonTestClass
{
    /**
     * @param string[] $path
     * @param int $size
     * @param string $type
     * @param bool $read
     * @param bool $write
     * @dataProvider dataProvider
     */
    public function testSimpleNode(array $path, int $size, string $type, bool $read, bool $write, bool $asFile, bool $asDir, bool $asLink): void
    {
        $lib = new FileNode();
        $lib->setData($path, $size, $type, $read, $write);
        $this->assertEquals($path, $lib->getPath());
        $this->assertEquals($size, $lib->getSize());
        $this->assertEquals($type, $lib->getType());
        $this->assertEquals($read, $lib->isReadable());
        $this->assertEquals($write, $lib->isWritable());
        $this->assertEquals($asFile, $lib->isFile());
        $this->assertEquals($asDir, $lib->isDir());
        $this->assertEquals($asLink, $lib->isLink());
    }

    public function dataProvider(): array
    {
        return [
            [['abc', 'def', 'ghi'], 123, ITypes::TYPE_UNKNOWN, false, false, false, false, false],
            [['jkl', ], 456, ITypes::TYPE_DIR, false, true, false, true, false],
            [['mno', 'pqr', ], 789, ITypes::TYPE_LINK, false, true, false, false, true],
        ];
    }

    public function testSubNodes(): void
    {
        $lib = new FileNode();
        $lib->setData(['abcdef', 'abcdef'], 0, '', true, false);
        $sub = new FileNode();
        $sub->setData(['ghijkl', 'ghijkl'], 0, '', false, true);
        $lib->addSubNode($sub);
        $inside = $lib->getSubNodes();
        $this->assertEquals(1, count($inside));
        $this->assertEquals(['ghijkl', 'ghijkl'], reset($inside)->getPath());
    }
}
