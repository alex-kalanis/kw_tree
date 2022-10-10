<?php

namespace kalanis\kw_tree;


use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\IDataSource;


/**
 * class Tree
 * @package kalanis\kw_tree
 * Main class for work with trees
 */
class Tree
{
    /** @var IDataSource */
    protected $dataSource = null;
    /** @var Essentials\FileNode|null */
    protected $loadedTree = null;
    /** @var callback|callable|null */
    protected $nodesCallback = null;

    public function __construct(IDataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function canRecursive(bool $recursive): void
    {
        $this->dataSource->canRecursive($recursive);
    }

    public function startFromPath(string $path): void
    {
        $this->dataSource->startFromPath($path);
    }

    /**
     * @param callback|callable|null $callback
     */
    public function setFilterCallback($callback = null): void
    {
        $this->dataSource->setFilterCallback($callback);
    }

    /**
     * @param callback|callable|null $callback
     */
    public function setNodesCallback($callback = null): void
    {
        $this->nodesCallback = $callback;
    }

    public function process(): void
    {
        $this->dataSource->process();
        $nodes = $this->dataSource->getNodes();
        /** @var Essentials\FileNode[] $nodes */

        if ($this->nodesCallback) {
            $nodes = array_filter($nodes, $this->nodesCallback);
        }

print_r($nodes);
        foreach ($nodes as $index => &$node) {
            if ('' != $index) { // not parent for root
                $dir = $this->dirPathToString($node);
                if ($nodes[$dir] !== $node) { // beware of unintended recursion
                    $nodes[$dir]->addSubNode($node); // and now only to parent dir
                }
            }
        }
        $this->loadedTree = $nodes[''];
//print_r($this->loadedTree);
    }

    protected function dirPathToString(FileNode $node): string
    {
        return implode(IPaths::SPLITTER_SLASH, array_slice($node->getPath(), 0, -1));
    }

    public function getTree(): ?Essentials\FileNode
    {
        return $this->loadedTree;
    }
}
