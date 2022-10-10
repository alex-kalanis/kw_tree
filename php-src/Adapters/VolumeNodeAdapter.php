<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\Essentials\FileNode;
use SplFileInfo;


/**
 * Class VolumeNodeAdapter
 * @package kalanis\kw_tree\Adapters
 * Create tree node from SplFileInfo

Normal file
path - the whole path against cutDir
 *
Normal dir
path - the whole path against cutDir
 *
Root dir for lookup is a bit different:
path - empty
 */
class VolumeNodeAdapter
{
    protected $cutDir = '';

    public function cutDir(string $dir): self
    {
        $check = realpath($dir);
        if (false !== $check) {
            $this->cutDir = $check . DIRECTORY_SEPARATOR;
        }
        return $this;
    }

    public function process(SplFileInfo $info): FileNode
    {
        $pathToCut = $this->shortRealPath($info);
        $path = $this->cutPath($pathToCut);

//print_r(['info' => $info, 'path' => $pathToCut, 'cut' => $path, 'dir' => $dir, 'name' => $name]);
        $node = new FileNode();
        $node->setData(
            array_filter(array_filter(Stuff::pathToArray($path), ['\kalanis\kw_paths\Stuff', 'notDots'])),
            $info->getSize(),
            $info->getType(),
            $info->isReadable(),
            $info->isWritable()
        );
        return $node;
    }

    protected function shortRealPath(SplFileInfo $info): string
    {
        $path = $info->getRealPath();
        return $info->isDir() && (false === mb_strpos($path, $this->cutDir))
            ? Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR
            : $path
        ;
    }

    protected function cutPath(string $path): string
    {
        return (0 === mb_strpos($path, $this->cutDir))
            ? mb_substr($path, mb_strlen($this->cutDir))
            : $path
        ;
    }
}
