<?php

namespace kalanis\kw_tree\DataSources;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_tree\Adapters;
use kalanis\kw_tree\Interfaces\IDataSource;
use kalanis\kw_tree\Interfaces\ITree;


/**
 * Class Files
 * @package kalanis\kw_tree\DataSources
 * Tree source is in remote Storage defined in Files
 *
 * separator is (usually) DIRECTORY_SEPARATOR
 * - when recursive got everything
 * - when solo filter only to records which has no separator after start path
 */
class Files extends ADataStorage implements IDataSource
{
    /** @var IProcessDirs */
    protected $dirTree = null;
    /** @var Adapters\FilesNodeAdapter */
    protected $nodeAdapter = null;
    /** @var string[] */
    protected $startFromPath = [];
    /** @var string */
    protected $dirDelimiter = IPaths::SPLITTER_SLASH;

    public function __construct(IProcessDirs $dirTree, IProcessNodes $nodeProcessor)
    {
        $this->dirTree = $dirTree;
        $this->nodeAdapter = new Adapters\FilesNodeAdapter($nodeProcessor);
    }

    public function startFromPath(array $path): void
    {
        $this->startFromPath = array_filter($path);
    }

    /**
     * @throws FilesException
     */
    public function process(): void
    {
        $iter = $this->dirTree->readDir($this->startFromPath, $this->loadRecursive);
        if ($this->filterCallback) {
            $iter = array_filter($iter, $this->filterCallback);
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
            $item = new Node();
            $item->setData($this->startFromPath, 0, ITypes::TYPE_DIR);
            $rootNode = $this->nodeAdapter->process($item);
            $nodes[''] = $rootNode; // root node
        }
        $this->nodes = $nodes;
    }

    protected function getDirKey(array $path): string
    {
        return (0 < count($path)) ? implode($this->dirDelimiter, array_slice($path, 0, -1)) : '' ;
    }

    public function filterDoubleDot(string $name): bool
    {
        return ( ITree::PARENT_DIR != $name ) ;
    }
}
