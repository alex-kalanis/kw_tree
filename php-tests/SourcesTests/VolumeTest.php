<?php

namespace SourcesTests;


use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_paths\PathsException;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree\DataSources\Volume;


/**
 * Class VolumeTest
 * @package SourcesTests
 */
class VolumeTest extends \CommonTestClass
{
    /**
     * @throws PathsException
     */
    public function testSimple(): void
    {
        $results = $this
            ->getLib()
            ->process()
            ->getRoot();
        $this->assertNotEmpty($results);
        $sub = $results->getSubNodes();
        $this->assertNotEmpty($sub);
    }

    /**
     * @throws PathsException
     */
    public function testFilesAllCallback(): void
    {
        $results = $this
            ->getLib()
            ->wantDeep(true)
            ->setFilterCallback([$this, 'justDirsCallback'])
            ->setOrdering(ITree::ORDER_DESC)
            ->process()
            ->getRoot();
        $this->assertNotEmpty($results);
        $sub = $results->getSubNodes();
        $this->assertNotEmpty($sub);

        $node = reset($sub);
        /** @var FileNode $node */
        $this->assertEquals(['sub'], $node->getPath());
        $this->assertEquals(ITypes::TYPE_DIR, $node->getType());
        $this->assertTrue($node->isReadable());
        $this->assertTrue($node->isWritable());
        $this->assertEmpty($node->getSubNodes());

        $node = next($sub);
        $this->assertEquals(['next_one'], $node->getPath());
        $this->assertNotEmpty($node->getSubNodes());

        $subs = $node->getSubNodes();
        $node = reset($subs);
        $this->assertEquals(['next_one', 'sub_one'], $node->getPath());
        $this->assertEmpty($node->getSubNodes());

        $this->assertFalse(next($subs));

        $node = next($sub);
        $this->assertEquals(['last_one'], $node->getPath());
        $this->assertEmpty($node->getSubNodes());

        $this->assertFalse(next($sub));
    }

    /**
     * @throws PathsException
     */
    public function testFilesLevel(): void
    {
        $results = $this
            ->getLib()
            ->wantDeep(false)
            ->setFilterCallback([$this, 'justFilesCallback'])
            ->setOrdering(ITree::ORDER_ASC)
            ->process()
            ->getRoot();
        $this->assertNotEmpty($results);
        $this->assertEquals([], $results->getPath());
        $this->assertEquals(ITypes::TYPE_DIR, $results->getType());
        $this->assertNotEmpty($results->getSubNodes());

        /** @var FileNode $results */
        $sub = $results->getSubNodes();

        $node = reset($sub);
        /** @var FileNode $node */
        $this->assertEquals(['dummy1.txt'], $node->getPath());
        $this->assertEquals(ITypes::TYPE_FILE, $node->getType());
        $this->assertTrue($node->isReadable());
        $this->assertTrue($node->isWritable());
        $this->assertEmpty($node->getSubNodes());
    }

    public function justDirsCallback(\SplFileInfo $node): bool
    {
        return $node->isDir();
    }

    public function justFilesCallback(\SplFileInfo $node): bool
    {
        return $node->isFile();
    }

    /**
     * @throws PathsException
     */
    public function testReversedShallow(): void
    {
        $results = $this
            ->getLib()
            ->wantDeep(false)
            ->setStartPath(['sub'])
            ->setOrdering(ITree::ORDER_ASC)
            ->process()
            ->getRoot();
        $this->assertNotEmpty($results);
    }

    /**
     * @throws PathsException
     */
    public function testNothing(): void
    {
        $lib = $this
            ->getLib()
            ->setStartPath(['path', 'does not', 'exists'])
            ->setOrdering(ITree::ORDER_NONE)
            ->process();
        $this->assertEmpty($lib->getRoot());
    }

    /**
     * @throws PathsException
     * @return Volume
     */
    protected function getLib(): Volume
    {
        return new Volume(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'data' . DIRECTORY_SEPARATOR . 'tree');
    }
}
