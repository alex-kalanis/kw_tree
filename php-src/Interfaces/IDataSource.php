<?php

namespace kalanis\kw_tree\Interfaces;


use kalanis\kw_tree\Essentials\FileNode;


/**
 * Interface IDataSource
 * @package kalanis\kw_tree\Interfaces
 */
interface IDataSource
{
    public function startFromPath(string $path): void;

    public function canRecursive(bool $recursive): void;

    /**
     * @param callback|callable|null $callback
     */
    public function setFilterCallback($callback = null): void;

    public function process(): void;

    /**
     * @return array<string, FileNode>
     */
    public function getNodes(): array;
}
