<?php

namespace kalanis\kw_tree\Traits;


use kalanis\kw_files\Node;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Stuff;


/**
 * Trait TFilesFiles
 * @package kalanis\kw_tree\Traits
 * Prepared callbacks for usage with data sources
 */
trait TFilesFiles
{
    public function filesExtCallback(Node $node): bool
    {
        $arrPt = new ArrayPath();
        $arrPt->setArray($node->getPath());
        $ext = Stuff::fileExt($arrPt->getFileName());
        return $node->isDir() || ($node->isFile() && in_array($ext, $this->whichExtsIWant()));
    }

    /**
     * @return string[]
     */
    abstract protected function whichExtsIWant(): array;
}
