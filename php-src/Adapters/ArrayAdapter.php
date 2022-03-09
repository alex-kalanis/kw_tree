<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_tree\FileNode;


/**
 * Class NodeAdapter
 * @package kalanis\kw_tree\Adapters
 * Create node from data array and reverse
 */
class ArrayAdapter
{
    public function pack(FileNode $node): array
    {
        return [
            'name' => $node->getName(),
            'dir' => $node->getDir(),
            'path' => $node->getPath(),
            'size' => $node->getSize(),
            'type' => $node->getType(),
            'read' => intval($node->isReadable()),
            'write' => intval($node->isWritable()),
            'sub' => array_map([$this, 'pack'], $node->getSubNodes()),
        ];
    }

    public function unpack(array $array): FileNode
    {
        $node = new FileNode();
        $node->setData(
            $array['name'],
            $array['dir'],
            $array['path'],
            intval($array['size']),
            $array['type'],
            boolval($array['read']),
            boolval($array['write'])
        );
        foreach ($array['sub'] as $item) {
            $node->addSubNode($this->unpack($item));
        }
        return $node;
    }
}
