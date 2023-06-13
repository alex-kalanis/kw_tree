<?php

namespace kalanis\kw_tree\DataSources;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;


/**
 * Class Files
 * @package kalanis\kw_tree\DataSources
 * The source is in libraries kw_files and their composite adapter
 */
class Files extends ASources
{
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var ArrayPath */
    protected $libPath = null;

    public function __construct(CompositeAdapter $files)
    {
        $this->files = $files;
        $this->libPath = new ArrayPath();
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return $this
     */
    public function process(): ITree
    {
        if (!$this->files->exists($this->startPath)) {
            return $this;
        }

        $entries = $this->files->readDir($this->startPath, $this->recursive);

        if ($this->filterCallback) {
            $entries = array_filter($entries, $this->filterCallback);
        }

        /** @var FileNode[] $nodes */
        $nodes = [];
        // sometimes the root node is filtered out - put it there for each situation
        $initNode = new Node();
        $initNode->setData([], 0, ITypes::TYPE_DIR);
        $nodes[''] = $this->fillNode($initNode);

        // loaded into nodes
        foreach ($entries as $entry) {
            /** @var Node $entry */
            $key = $this->libPath->setArray($entry->getPath())->getString();
            $nodes[$key] = $this->fillNode($entry);
        }

        // sort obtained
        if (ITree::ORDER_NONE != $this->ordering) {
            uasort(
                $nodes,
                (ITree::ORDER_ASC == $this->ordering ? [$this, 'orderUp'] : [$this, 'orderDown'])
            );
        }

        // now create tree
        foreach ($nodes as $node) {
            $parentPath = $this->libPath->setArray($node->getPath())->getStringDirectory();
            if (!empty($node->getPath()) && isset($nodes[$parentPath])) {
                $nodes[$parentPath]->addSubNode($node);
            }
        }

        $this->startNode = $nodes[''];
        return $this;
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
     * @param Node $file
     * @throws FilesException
     * @throws PathsException
     * @return FileNode
     */
    protected function fillNode(Node $file): FileNode
    {
        $node = new FileNode();
        return $node->setData(
            $this->libPath->setArray($file->getPath())->getArray(),
            $file->getSize(),
            $file->getType(),
            $this->files->isReadable($file->getPath()),
            $this->files->isWritable($file->getPath())
        );
    }
}
