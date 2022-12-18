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
        return empty($node->getPath())
            ? ''
            : $this->getDirKey($node->getPath())
        ;
    }

    /**
     * @param string[] $path
     * @return string
     */
    abstract protected function getDirKey(array $path): string;

    /**
     * @return array<string, FileNode>
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
}
