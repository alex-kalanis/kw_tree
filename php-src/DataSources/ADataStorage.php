<?php

namespace kalanis\kw_tree\DataSources;


use kalanis\kw_tree\Essentials\FileNode;


/**
 * Class ADataStorage
 * @package kalanis\kw_tree\DataSources
 * Filter tree only for directories
 */
abstract class ADataStorage
{
    /** @var bool */
    protected $loadRecursive = false;
    /** @var callback|callable|null */
    protected $filterCallback = null;
    /** @var array<string, FileNode> */
    protected $nodes = [];

    public function canRecursive(bool $recursive): void
    {
        $this->loadRecursive = $recursive;
    }

    /**
     * @param callback|callable|null $callback
     */
    public function setFilterCallback($callback = null): void
    {
        $this->filterCallback = $callback;
    }

    protected function getKey(FileNode $node): string
    {
        return $node->isDir()
            ? (empty($node->getPath())
                ? $node->getName()
                : $this->getDirKey($node->getPath())
            )
            : $node->getPath()
        ;
    }

    abstract protected function getDirKey(string $path): string;

    public function getNodes(): array
    {
        return $this->nodes;
    }
}
