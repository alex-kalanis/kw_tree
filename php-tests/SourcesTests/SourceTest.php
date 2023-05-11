<?php

namespace SourcesTests;


use kalanis\kw_tree\Interfaces\ITree;
use kalanis\kw_tree\DataSources\ASources;


class SourceTest extends \CommonTestClass
{
    public function testStartPath(): void
    {
        $lib = new XSource();
        $this->assertEquals([], $lib->getStartPath());
        $lib->setStartPath(['abc', 'def']);
        $this->assertEquals(['abc', 'def'], $lib->getStartPath());
    }

    public function testOrdering(): void
    {
        $lib = new XSource();
        $this->assertEquals(ITree::ORDER_NONE, $lib->getOrdering());
        $lib->setOrdering('other');
        $this->assertEquals(ITree::ORDER_NONE, $lib->getOrdering());
        $lib->setOrdering('ASC');
        $this->assertEquals(ITree::ORDER_ASC, $lib->getOrdering());
    }

    public function testCallback(): void
    {
        $lib = new XSource();
        $this->assertEmpty($lib->getFilterCallback());
        $lib->setFilterCallback(['abc', 'def']);
        $this->assertEquals(['abc', 'def'], $lib->getFilterCallback());
    }

    public function testRecursive(): void
    {
        $lib = new XSource();
        $this->assertFalse($lib->isRecursive());
        $lib->wantDeep(true);
        $this->assertTrue($lib->isRecursive());
    }

    public function testNode(): void
    {
        $lib = new XSource();
        $lib->process();
        $this->assertEmpty($lib->getRoot());
    }
}


class XSource extends ASources
{
    public function process(): ITree
    {
        return $this;
    }

    /**
     * @return string[]
     */
    public function getStartPath(): array
    {
        return $this->startPath;
    }

    public function getOrdering(): string
    {
        return $this->ordering;
    }

    /**
     * @return callable|null
     */
    public function getFilterCallback()
    {
        return $this->filterCallback;
    }

    public function isRecursive(): bool
    {
        return $this->recursive;
    }
}
