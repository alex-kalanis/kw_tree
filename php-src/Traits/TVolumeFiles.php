<?php

namespace kalanis\kw_tree\Traits;


use kalanis\kw_paths\Stuff;
use SplFileInfo;


/**
 * Trait TVolumeFiles
 * @package kalanis\kw_tree\Traits
 * Prepared callbacks for usage with data sources
 */
trait TVolumeFiles
{
    public function filesExtCallback(SplFileInfo $node): bool
    {
        $ext = Stuff::fileExt($node->getFileName());
        return $node->isDir() || ($node->isFile() && in_array($ext, $this->whichExtsIWant()));
    }

    /**
     * @return string[]
     */
    abstract protected function whichExtsIWant(): array;
}
