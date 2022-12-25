<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_files\FilesException;
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
    /** @var string[] */
    protected $cutDir = [];

    /**
     * @param string[] $dir
     * @throws FilesException
     * @return VolumeNodeAdapter
     */
    public function cutDir(array $dir): self
    {
        $check = realpath(Stuff::arrayToPath($dir));
        $this->cutDir = (false !== $check) ? Stuff::pathToArray($check) : [];
        return $this;
    }

    /**
     * @param SplFileInfo $info
     * @throws FilesException
     * @return FileNode|null
     */
    public function process(SplFileInfo $info): ?FileNode
    {
        $path = $this->cutArrayPath(Stuff::pathToArray($info->getRealPath()));
        if (is_null($path)) {
            return null;
        }

//print_r(['info' => $info, 'path' => $path, ]);
        $node = new FileNode();
        $node->setData(
            $path,
            $info->getSize(),
            $info->getType(),
            $info->isReadable(),
            $info->isWritable()
        );
        return $node;
    }

    /**
     * @param string[] $path
     * @return string[]|null
     */
    protected function cutArrayPath(array $path): ?array
    {
        if (empty($this->cutDir)) {
            return $path;
        }
        foreach ($this->cutDir as $pos => $cut) {
            if (!isset($path[$pos])) {
                return null;
            }
            if ($path[$pos] != $cut) {
                return null;
            }
        }

        return array_slice($path, count($this->cutDir));
    }
}
