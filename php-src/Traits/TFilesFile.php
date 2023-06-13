<?php

namespace kalanis\kw_tree\Traits;


use kalanis\kw_files\Node;


/**
 * Trait TFilesFile
 * @package kalanis\kw_tree\Traits
 * Prepared callbacks for usage with data sources
 * Return only nodes identified as files
 */
trait TFilesFile
{
    public function justFilesCallback(Node $node): bool
    {
        return $node->isFile();
    }
}
