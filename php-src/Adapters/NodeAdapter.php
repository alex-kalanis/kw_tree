<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\FileNode;
use SplFileInfo;


/**
 * Class NodeAdapter
 * @package kalanis\kw_tree\Adapters
 * Create tree node from SplFileInfo

Normal file
path - the whole path against cutDir
name - name of that file
dir - upper directory with ending slash; can be only slash for root
 *
Normal dir
path - the whole path against cutDir
name - name of that dir without slash
dir - upper directory with ending slash
 *
Root dir for lookup is a bit different:
path - empty
name - slash
dir - slash

 */
class NodeAdapter
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

        if (empty($path)) {
            // root
            $name = DIRECTORY_SEPARATOR;
            $dir = DIRECTORY_SEPARATOR;
        } else {
            // other dirs, files, pipes, etc...
            $name = Stuff::filename($path);  // DO NOT USE $info->getFilename() -> for dir it returns '.' !!!
            $dir = Stuff::directory($path);
            $dir = empty($dir) ? '' : Stuff::removeEndingSlash($dir) . DIRECTORY_SEPARATOR;
        }

//print_r(['info' => $info, 'path' => $pathToCut, 'cut' => $path, 'dir' => $dir, 'name' => $name]);
        $node = new FileNode();
        $node->setData(
            $path,
            $dir,
            $name,
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
