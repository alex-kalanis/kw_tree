<?php

namespace SourcesTests;


use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\ITarget;
use kalanis\kw_storage\Storage\Key\StaticPrefixKey;
use kalanis\kw_storage\Storage\Storage;
use kalanis\kw_storage\Storage\Target\Memory;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree\DataSources\Files;


/**
 * Class FilesTest
 * @package SourcesTests
 */
class FilesTest extends \CommonTestClass
{
    /**
     * @throws FilesException
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
     * @throws FilesException
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
     * @throws FilesException
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

    public function justDirsCallback(Node $node): bool
    {
        return $node->isDir();
    }

    public function justFilesCallback(Node $node): bool
    {
        return $node->isFile();
    }

    /**
     * @throws FilesException
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
     * @throws FilesException
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
     * @throws FilesException
     * @throws PathsException
     * @return Files
     */
    protected function getLib(): Files
    {
        $compFact = new Factory();
        StaticPrefixKey::setPrefix(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree');
        $storage = new Storage(new StaticPrefixKey(), $this->filledMemory());
        return new Files($compFact->getClass($storage));
    }

    protected function filledMemory(): ITarget
    {
        $lib = new Memory();
        $lib->save(DIRECTORY_SEPARATOR . 'data', IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree', IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'last_one', IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'last_one' . DIRECTORY_SEPARATOR . '.gitkeep', '');
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'sub', IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'dummy3.txt', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'dummy4.bin', false); // intentionally!!!
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'next_one', IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'next_one' . DIRECTORY_SEPARATOR . 'sub_one', IProcessNodes::STORAGE_NODE_KEY);
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'next_one' . DIRECTORY_SEPARATOR . 'sub_one' . DIRECTORY_SEPARATOR . '.gitkeep', '');
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'dummy1.txt', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'other2.doc', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'other1.txt', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        $lib->save(DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR . 'dummy2.doc', 'qwertzuiopasdfghjklyxcvbnm0123456789');
        return $lib;
    }
}
