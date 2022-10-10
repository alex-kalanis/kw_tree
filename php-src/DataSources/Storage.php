<?php

namespace kalanis\kw_tree\DataSources;


use CallbackFilterIterator;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_tree\Adapters;
use kalanis\kw_tree\Interfaces\IDataSource;
use kalanis\kw_tree\Interfaces\ITree;


/**
 * Class Storage
 * @package kalanis\kw_tree\DataSources
 * Tree source is in remote Storage
 *
 * separator is (usually) DIRECTORY_SEPARATOR
 * - when recursive got everything
 * - when solo filter only to records which has no separator after start path
 */
class Storage extends ADataStorage implements IDataSource
{
    /** @var IStorage */
    protected $storage = null;
    /** @var Adapters\StorageNodeAdapter */
    protected $nodeAdapter = null;
    protected $startFromPath = '';
    protected $dirDelimiter = IPaths::SPLITTER_SLASH;

    public function __construct(IStorage $storage)
    {
        $this->storage = $storage;
        $this->nodeAdapter = new Adapters\FilesNodeAdapter();
    }

    public function startFromPath(string $path): void
    {
        $this->startFromPath = $path;
    }

    public function process(): void
    {
        $iter = $this->loadRecursive ? $this->getRecursive() : $this->getFlat() ;
        $iter = new CallbackFilterIterator($iter, [$this, 'filterDoubleDot']);
        if ($this->filterCallback) {
            $iter = new CallbackFilterIterator($iter, $this->filterCallback);
        }

        $nodes = [];
        foreach ($iter as $item) {
            $eachNode = $this->nodeAdapter->process($item);
            $nodes[$this->getKey($eachNode)] = $eachNode; // full path
        }
        if (isset($nodes[$this->dirDelimiter])) {
            $nodes[''] = $nodes[$this->dirDelimiter];
            unset($nodes[$this->dirDelimiter]);
        }
        if (empty($nodes[''])) { // root dir has no upper path
            $item = new SplFileInfo($this->rootDir . $this->startFromPath);
            $rootNode = $this->nodeAdapter->process($item);
            $nodes[''] = $rootNode; // root node
        }
        $this->nodes = $nodes;

    }

    protected function getDirKey(array $path): string
    {
        return (0 < count($path)) ? implode($this->dirDelimiter, array_slice($path, 0, -1)) : '' ;
    }

    protected function getFlat(): \Traversable
    {
        foreach ($this->storage->lookup($this->startFromPath) as $name) {
            if (!empty($this->startFromPath)) {
                $name = str_replace($this->startFromPath, '', $name);
            }
            yield $name;
        }
    }

    protected function getRecursive(): \Traversable
    {
        foreach ($this->storage->lookup($this->startFromPath) as $name) {
            yield $name;
        }
    }

    public function filterDoubleDot(string $name): bool
    {
        return ( ITree::PARENT_DIR != $name ) ;
    }
}
