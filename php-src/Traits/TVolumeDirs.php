<?php

namespace kalanis\kw_tree\Traits;


use SplFileInfo;


/**
 * Trait TVolumeDirs
 * @package kalanis\kw_tree\Traits
 * Prepared callbacks for usage with volume
 * Return only nodes identified as dirs
 */
trait TVolumeDirs
{
    public function justDirsCallback(SplFileInfo $node): bool
    {
        return $node->isDir();
    }
}
