<?php

namespace BasicTests;


use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_tree\Essentials\FileNode;


class NodeTest extends \CommonTestClass
{
    public function testBasic(): void
    {
        $lib = new FileNode();
        $this->assertEquals([], $lib->getSubNodes());
        $this->assertEquals([], $lib->getPath());
        $this->assertEquals(0, $lib->getSize());
        $this->assertEquals(ITypes::TYPE_UNKNOWN, $lib->getType());
        $this->assertFalse($lib->isReadable());
        $this->assertFalse($lib->isWritable());
        $this->assertFalse($lib->isFile());
        $this->assertFalse($lib->isDir());
        $this->assertFalse($lib->isLink());

        $lib->setData(['foo', 'bar', 'baz'], 33, ITypes::TYPE_LINK, false, true);
        $this->assertEquals([], $lib->getSubNodes());
        $this->assertEquals(['foo', 'bar', 'baz'], $lib->getPath());
        $this->assertEquals(33, $lib->getSize());
        $this->assertEquals(ITypes::TYPE_LINK, $lib->getType());
        $this->assertFalse($lib->isReadable());
        $this->assertTrue($lib->isWritable());
        $this->assertFalse($lib->isFile());
        $this->assertFalse($lib->isDir());
        $this->assertTrue($lib->isLink());

        $lib->setData(['foo', 'baz'], 813, ITypes::TYPE_DIR, true, true);
        $this->assertEquals([], $lib->getSubNodes());
        $this->assertEquals(['foo', 'baz'], $lib->getPath());
        $this->assertEquals(813, $lib->getSize());
        $this->assertEquals(ITypes::TYPE_DIR, $lib->getType());
        $this->assertTrue($lib->isReadable());
        $this->assertTrue($lib->isWritable());
        $this->assertFalse($lib->isFile());
        $this->assertTrue($lib->isDir());
        $this->assertFalse($lib->isLink());
    }

    public function testSubNodes(): void
    {
        $lib1 = new FileNode();
        $this->assertEquals([], $lib1->getSubNodes());
        $this->assertEquals([], $lib1->getPath());
        $this->assertEquals(0, $lib1->getSize());
        $this->assertEquals(ITypes::TYPE_UNKNOWN, $lib1->getType());
        $this->assertFalse($lib1->isReadable());
        $this->assertFalse($lib1->isWritable());
        $this->assertFalse($lib1->isFile());
        $this->assertFalse($lib1->isDir());
        $this->assertFalse($lib1->isLink());

        $lib2 = new FileNode();
        $lib2->setData(['foo', 'bar', 'baz'], 33, ITypes::TYPE_LINK, false, true);
        $this->assertEquals([], $lib2->getSubNodes());
        $this->assertEquals(['foo', 'bar', 'baz'], $lib2->getPath());
        $this->assertEquals(33, $lib2->getSize());
        $this->assertEquals(ITypes::TYPE_LINK, $lib2->getType());
        $this->assertFalse($lib2->isReadable());
        $this->assertTrue($lib2->isWritable());
        $this->assertFalse($lib2->isFile());
        $this->assertFalse($lib2->isDir());
        $this->assertTrue($lib2->isLink());

        $lib3 = new FileNode();
        $lib3->setData(['foo', 'baz'], 813, ITypes::TYPE_DIR, true, true);
        $this->assertEquals([], $lib3->getSubNodes());
        $this->assertEquals(['foo', 'baz'], $lib3->getPath());
        $this->assertEquals(813, $lib3->getSize());
        $this->assertEquals(ITypes::TYPE_DIR, $lib3->getType());
        $this->assertTrue($lib3->isReadable());
        $this->assertTrue($lib3->isWritable());
        $this->assertFalse($lib3->isFile());
        $this->assertTrue($lib3->isDir());
        $this->assertFalse($lib3->isLink());

        $lib1->addSubNode($lib2);
        $lib1->addSubNode($lib3);
        $this->assertNotEmpty($lib1->getSubNodes());
    }
}
