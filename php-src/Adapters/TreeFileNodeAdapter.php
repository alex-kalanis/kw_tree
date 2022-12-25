<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_tree\Essentials\FileNode;


/**
 * Class TreeFileNodeAdapter
 * @package kalanis\kw_tree\Adapters
 * Create our node from File Node and reverse
 */
class TreeFileNodeAdapter
{
    /**
     * @param FileNode $node
     * @return array<string, string|int|array<string, string|int|mixed>>
     */
    public function pack(FileNode $node): array
    {
        return [
            'path' => $node->getPath(),
            'size' => $node->getSize(),
            'type' => $node->getType(),
            'read' => intval($node->isReadable()),
            'write' => intval($node->isWritable()),
            'sub' => array_map([$this, 'pack'], $node->getSubNodes()),
        ];
    }

    /**
     * @param array<string, string|int|array<string, string|int|mixed>> $array
     * @return FileNode
     */
    public function unpack(array $array): FileNode
    {
        $node = new FileNode();
        $node->setData(
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
