<?php

namespace kalanis\kw_tree\Traits;


use SplFileInfo;


/**
 * Trait TVolumeFile
 * @package kalanis\kw_tree\Traits
 * Prepared callbacks for usage with volume
 * Return only nodes identified as files
 */
trait TVolumeFile
{
    public function justFilesCallback(SplFileInfo $node): bool
    {
        return $node->isFile();
    }
}
