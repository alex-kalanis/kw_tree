<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\FileNode;
use kalanis\kw_tree\Interfaces\ITree;
use SplFileInfo;


/**
 * Class NodeAdapter
 * @package kalanis\kw_tree\Adapters
 * Create tree node from SplFileInfo
path - the whole path against cutDir
name - name of that none
dir - upper directory with ending slash
 */
class NodeAdapter
{
    protected $cutDir = '';

    public function cutDir(string $dir): self
    {
        $check = realpath($dir);
        if (false !== $check) {
            $this->cutDir = $check;
        }
        return $this;
    }

    public function process(SplFileInfo $info): FileNode
    {
        $node = new FileNode();
        $path = $this->cutPath($info->getRealPath());
        $dir = Stuff::removeEndingSlash(Stuff::directory($path));
        if (ITree::CURRENT_DIR == $info->getFilename()) {
            $name = Stuff::filename($path);
            $node->setData(
                empty($name) ? DIRECTORY_SEPARATOR : $name,
                empty($dir) ? DIRECTORY_SEPARATOR : $dir,
                $path,
                $info->getSize(),
                $info->getType(),
                $info->isReadable(),
                $info->isWritable()
            );
        } else {
            $node->setData(
                $info->getFilename(),
                empty($dir) ? DIRECTORY_SEPARATOR : $dir,
                $path,
                $info->getSize(),
                $info->getType(),
                $info->isReadable(),
                $info->isWritable()
            );
        }
        return $node;
    }

    protected function cutPath(string $path): string
    {
        return (0 === mb_strpos($path, $this->cutDir))
            ? mb_substr($path, mb_strlen($this->cutDir))
            : $path
        ;
    }
}
