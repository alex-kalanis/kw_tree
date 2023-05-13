<?php

namespace SourcesTests;


use kalanis\kw_paths\PathsException;
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
    }

    public function justDirsCallback(\SplFileInfo $node): bool
    {
        return $node->isDir();
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
