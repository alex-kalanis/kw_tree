<?php

namespace kalanis\kw_tree\Filters;


use kalanis\kw_tree\FileNode;


/**
 * Class DirFilter
 * @package kalanis\kw_tree\Filters
 * Filter tree only for directories
 */
class DirFilter
{
    public function filter(FileNode $baseNode): ?FileNode
    {
        if (!$baseNode->isDir()) {
            return null;
        }

        $node = new FileNode(); // original one has everything - and I want only a few things
        $node->setData(
            $baseNode->getPath(),
            $baseNode->getDir(),
            $baseNode->getName(),
            $baseNode->getSize(),
            $baseNode->getType(),
            $baseNode->isReadable(),
            $baseNode->isWritable()
        );

        foreach ($baseNode->getSubNodes() as $subNode) {
            $sub = $this->filter($subNode);
            if ($sub) {
                $node->addSubNode($sub);
            }
        }
        return $node;
    }
}
