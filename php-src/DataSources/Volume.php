<?php

namespace kalanis\kw_tree\DataSources;


use CallbackFilterIterator;
use FilesystemIterator;
use Iterator;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;


class Volume extends ASources
{
    /** @var string */
    protected $systemDir = '';
    /** @var ArrayPath */
    protected $libPath = null;

    public function __construct(string $systemDir)
    {
        $this->systemDir = $systemDir;
        $this->libPath = new ArrayPath();
    }

    /**
     * @throws PathsException
     * @return $this
     */
    public function process(): ITree
    {
        $path = realpath($this->systemDir . DIRECTORY_SEPARATOR . $this->libPath->setArray($this->startPath)->getString());
        if (false === $path) {
            return $this;
        }

        $iter = $this->recursive ? $this->getRecursive($path) : $this->getFlat($path) ;
        $iter = new CallbackFilterIterator($iter, [$this, 'filterDoubleDot']);
        if ($this->filterCallback) {
            $iter = new CallbackFilterIterator($iter, $this->filterCallback);
        }

        /** @var FileNode[] $nodes */
        $nodes = [];
        $initNode = new SplFileInfo($path);
        $nodes[''] = $this->fillNode($initNode, '');

        foreach ($iter as $item) {
            /** @var SplFileInfo $item */
            $cutPath = $this->cutPathStart($path, $item->getRealPath());
            if (!is_null($cutPath)) {
                $nodes[$cutPath] = $this->fillNode($item, $cutPath);
            }
        }

        if (ITree::ORDER_NONE != $this->ordering) {
            uasort(
                $nodes,
                (ITree::ORDER_ASC == $this->ordering ? [$this, 'orderUp'] : [$this, 'orderDown'])
            );
        }

        foreach ($nodes as $node) {
            $parentPath = $this->libPath->setArray($node->getPath())->getStringDirectory();
            if (!empty($node->getPath()) && isset($nodes[$parentPath])) {
                $nodes[$parentPath]->addSubNode($node);
            }
        }

        $this->startNode = $nodes[''];
        return $this;
    }

    protected function getFlat(string $path): Iterator
    {
        return new FilesystemIterator($path);
    }

    protected function getRecursive(string $path): Iterator
    {
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    }

    public function filterDoubleDot(SplFileInfo $info): bool
    {
        return ( ITree::PARENT_DIR != $info->getFilename() ) ;
    }

    /**
     * @param FileNode $file1
     * @param FileNode $file2
     * @throws PathsException
     * @return int
     */
    public function orderUp(FileNode $file1, FileNode $file2): int
    {
        return strcasecmp(
            $this->libPath->setArray($file1->getPath())->getString(),
            $this->libPath->setArray($file2->getPath())->getString()
        );
    }

    /**
     * @param FileNode $file1
     * @param FileNode $file2
     * @throws PathsException
     * @return int
     */
    public function orderDown(FileNode $file1, FileNode $file2): int
    {
        return strcasecmp(
            $this->libPath->setArray($file2->getPath())->getString(),
            $this->libPath->setArray($file1->getPath())->getString()
        );
    }

    /**
     * @param SplFileInfo $file
     * @param string $path
     * @throws PathsException
     * @return FileNode
     */
    protected function fillNode(SplFileInfo $file, string $path): FileNode
    {
        $node = new FileNode();
        return $node->setData(
            $this->libPath->setString($path)->getArray(),
            $file->getSize(),
            $this->toType($file),
            $file->isReadable(),
            $file->isWritable()
        );
    }

    protected function cutPathStart(string $start, string $what): ?string
    {
        $isKnown = mb_strpos($what, $start);
        if (0 === $isKnown) {
            return mb_substr($what, mb_strlen($start));
        } else {
            // @codeCoverageIgnoreStart
            // false for unknown or higher number for elsewhere
            // this node will be kicked out of results later
            return null;
        }
        // @codeCoverageIgnoreEnd
    }

    protected function toType(SplFileInfo $file): string
    {
        switch ($file->getType()) {
            case 'dir':
                return ITypes::TYPE_DIR;
            case 'file':
                return ITypes::TYPE_FILE;
            // @codeCoverageIgnoreStart
            case 'link':
                return ITypes::TYPE_LINK;
            default:
                return ITypes::TYPE_UNKNOWN;
            // @codeCoverageIgnoreEnd
        }
    }
}
