<?php

namespace kalanis\kw_tree\Interfaces;


use kalanis\kw_tree\Essentials\FileNode;


/**
 * Interface ITree
 * @package kalanis\kw_tree\Interfaces
 * How to get internal tree structure from selected data source
 */
interface ITree
{
    const CURRENT_DIR = '.';
    const PARENT_DIR = '..';

    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';
    const ORDER_NONE = 'NONE';

    /**
     * Where to start in part known to data source
     * @param string[] $path
     * @return $this
     */
    public function setStartPath(array $path): self;

    /**
     * How to order entries
     * @param string $order
     * @param string|null $by
     * @return $this
     */
    public function setOrdering(string $order, ?string $by = null): self;

    /**
     * Filter read entries
     * @param callable|array<string, string>|string $callback
     * @return $this
     */
    public function setFilterCallback($callback): self;

    /**
     * Want more levels than just that defined in start path
     * @param bool $want
     * @return $this
     */
    public function wantDeep(bool $want): self;

    /**
     * Run lookup
     * @return $this
     */
    public function process(): self;

    /**
     * Get the root key as defined in start path or null if nothing found
     * @return FileNode|null
     */
    public function getRoot(): ?FileNode;
}
