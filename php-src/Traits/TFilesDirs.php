<?php

namespace kalanis\kw_tree\Traits;


use kalanis\kw_files\Node;


/**
 * Trait TFilesDirs
 * @package kalanis\kw_tree\Traits
 * Prepared callbacks for usage with data sources
 */
trait TFilesDirs
{
    public function justDirsCallback(Node $node): bool
    {
        return $node->isDir();
    }
}
