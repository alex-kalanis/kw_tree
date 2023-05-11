<?php

namespace kalanis\kw_tree\DataSources;


use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;


abstract class ASources implements ITree
{
    /** @var string[] */
    protected $startPath = [];
    /** @var string */
    protected $ordering = ITree::ORDER_NONE;
    /** @var callable|null */
    protected $filterCallback = null;
    /** @var bool */
    protected $recursive = false;
    /** @var FileNode|null */
    protected $startNode = null;

    public function setStartPath(array $path): ITree
    {
        $this->startPath = $path;
        return $this;
    }

    public function setOrdering(string $order, ?string $by = null): ITree
    {
        $order = strtoupper($order);
        $this->ordering = in_array($order, [ITree::ORDER_NONE, ITree::ORDER_DESC, ITree::ORDER_ASC]) ? $order : $this->ordering;
        return $this;
    }

    public function setFilterCallback($callback): ITree
    {
        $this->filterCallback = $callback;
        return $this;
    }

    public function wantDeep(bool $want): ITree
    {
        $this->recursive = $want;
        return $this;
    }

    public function getRoot(): ?FileNode
    {
        return $this->startNode;
    }
}
