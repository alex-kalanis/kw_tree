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
     * Filter over internal data source
     * @param callback|callable|null $callback
     */
    public function setFilterCallback($callback = null): void
    {
        $this->dataSource->setFilterCallback($callback);
    }

    /**
     * Filter over established nodes
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

//print_r($nodes);
        $tree = [];
        // paths to keys
        foreach ($nodes as &$node) {
            $tree[$this->currentDirPathToString($node)] = $node;
        }
//print_r($tree);

        // add subnodes
        foreach ($nodes as &$node) {
            $parentDir = $this->parentDirPathToString($node);
            if ($tree[$parentDir] !== $node) { // beware of unintended recursion
                $tree[$parentDir]->addSubNode($node); // and now only to parent dir
            }
        }

        // set root node as result
        $this->loadedTree = $tree[''];
//print_r($this->loadedTree);
    }

    protected function currentDirPathToString(FileNode $node): string
    {
        return implode(IPaths::SPLITTER_SLASH, $node->getPath());
    }

    protected function parentDirPathToString(FileNode $node): string
    {
        return implode(IPaths::SPLITTER_SLASH, array_slice($node->getPath(), 0, -1));
    }

    public function getTree(): ?Essentials\FileNode
    {
        return $this->loadedTree;
    }
}
