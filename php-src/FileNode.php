<?php

namespace kalanis\kw_tree;


use kalanis\kw_tree\Interfaces\ITree;


/**
 * Class FileNode
 * @package kalanis\kw_tree
 * File in directory (could be directory too)
 * Different, yet similar to SplFileInfo because it's possible to pack and unpack the whole thing without access to real volume
 */
class FileNode
{
    protected $path = '';
    protected $dir = '';
    protected $name = '';
    protected $type = ITree::TYPE_UNKNOWN;
    protected $size = 0;
    protected $readable = false;
    protected $writable = false;
    /** @var FileNode[] */
    protected $subNodes = [];

    public function setData(string $path, string $dir, string $name, int $size, string $type, bool $readable, bool $writable): self
    {
        $this->path = $path;
        $this->dir = $dir;
        $this->name = $name;
        $this->size = $size;
        $this->type = $type;
        $this->readable = $readable;
        $this->writable = $writable;
        return $this;
    }

    public function addSubNode(FileNode $node): self
    {
        $this->subNodes[] = $node;
        return $this;
    }

    /**
     * @return FileNode[]
     */
    public function getSubNodes(): array
    {
        return $this->subNodes;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function isFile(): bool
    {
        return ITree::TYPE_FILE == $this->type;
    }

    public function isDir(): bool
    {
        return ITree::TYPE_DIR == $this->type;
    }

    public function isLink(): bool
    {
        return ITree::TYPE_LINK == $this->type;
    }
}
