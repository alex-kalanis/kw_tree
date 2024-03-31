<?php

namespace kalanis\kw_tree\Essentials;


use kalanis\kw_files\Interfaces\ITypes;


/**
 * Class FileNode
 * @package kalanis\kw_tree\Essentials
 * File in directory (could be directory too)
 * Different, yet similar to SplFileInfo because it's possible to pack and unpack the whole thing without access to the real volume
 * Also represents more than one data source
 */
class FileNode
{
    /** @var string[] */
    protected array $path = [];
    protected string $type = ITypes::TYPE_UNKNOWN;
    protected int $size = 0;
    protected bool $readable = false;
    protected bool $writable = false;
    /** @var FileNode[] */
    protected array $subNodes = [];

    /**
     * @param string[] $path
     * @param int $size
     * @param string $type
     * @param bool $readable
     * @param bool $writable
     * @return $this
     */
    public function setData(array $path, int $size, string $type, bool $readable, bool $writable): self
    {
        $this->path = $path;
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

    /**
     * @return string[]
     */
    public function getPath(): array
    {
        return $this->path;
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
        return ITypes::TYPE_FILE == $this->type;
    }

    public function isDir(): bool
    {
        return ITypes::TYPE_DIR == $this->type;
    }

    public function isLink(): bool
    {
        return ITypes::TYPE_LINK == $this->type;
    }
}
