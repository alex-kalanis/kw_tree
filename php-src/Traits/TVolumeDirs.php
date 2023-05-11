<?php

namespace kalanis\kw_tree\Traits;


use SplFileInfo;


/**
 * Trait TVolumeDirs
 * @package kalanis\kw_tree\Traits
 * Prepared callbacks for usage with volume
 */
trait TVolumeDirs
{
    public function justDirsCallback(SplFileInfo $node): bool
    {
        return $node->isDir();
    }
}
