<?php

namespace kalanis\kw_tree\DataSources;


use CallbackFilterIterator;
use FilesystemIterator;
use Iterator;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\Adapters;
use kalanis\kw_tree\Interfaces\IDataSource;
use kalanis\kw_tree\Interfaces\ITree;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use SplFileInfo;


/**
 * Class Volume
 * @package kalanis\kw_tree\DataSources
 * Read data directly from Volume
 */
class Volume extends ADataStorage implements IDataSource
{
    /** @var Adapters\VolumeNodeAdapter */
    protected $nodeAdapter = null;
    /** @var string */
    protected $rootDir = '';
    /** @var string */
    protected $startFromPath = '';

    public function __construct(Path $path)
    {
        $this->rootDir = realpath($path->getDocumentRoot() . $path->getPathToSystemRoot()) . DIRECTORY_SEPARATOR;
        $this->nodeAdapter = new Adapters\VolumeNodeAdapter();
    }

    public function startFromPath(string $path): void
    {
        if (false !== ($knownPath = realpath($this->rootDir . $path))) {
            $this->startFromPath = $path;
            $this->nodeAdapter->cutDir($knownPath . DIRECTORY_SEPARATOR);
        }
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
        if (isset($nodes[DIRECTORY_SEPARATOR])) {
            $nodes[''] = $nodes[DIRECTORY_SEPARATOR];
            unset($nodes[DIRECTORY_SEPARATOR]);
        }
        if (empty($nodes[''])) { // root dir has no upper path
            $item = new SplFileInfo($this->rootDir . $this->startFromPath);
            $rootNode = $this->nodeAdapter->process($item);
            $nodes[''] = $rootNode; // root node
        }
        $this->nodes = $nodes;
    }

    protected function getDirKey(string $path): string
    {
        return Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR;
    }

    protected function getFlat(): Iterator
    {
        return new FilesystemIterator($this->rootDir . $this->startFromPath);
    }

    protected function getRecursive(): Iterator
    {
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->rootDir . $this->startFromPath));
    }

    public function filterDoubleDot(SplFileInfo $info): bool
    {
        return ( ITree::PARENT_DIR != $info->getFilename() ) ;
    }
}
