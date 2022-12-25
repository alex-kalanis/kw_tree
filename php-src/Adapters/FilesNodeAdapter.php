<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_files\Node;
use kalanis\kw_tree\Essentials\FileNode;


/**
 * Class FilesNodeAdapter
 * @package kalanis\kw_tree\Adapters
 * Create tree node from Storage record (I have little to no information about Node)

Normal file
path - the whole path against cutDir
 *
Normal dir
path - the whole path against cutDir
 *
Root dir for lookup is a bit different:
path - empty
 */
class FilesNodeAdapter
{
    /** @var IProcessNodes */
    protected $nodeProcessor = null;
    /** @var string[] */
    protected $cutDir = [];

    public function __construct(IProcessNodes $nodeProcessor)
    {
        $this->nodeProcessor = $nodeProcessor;
    }

    /**
     * @param string[] $dir
     * @throws FilesException
     * @return $this
     */
    public function cutDir(array $dir): self
    {
        $this->cutDir = ($this->nodeProcessor->exists($dir) && $this->nodeProcessor->isDir($dir)) ? $dir : [];
        return $this;
    }

    public function process(Node $info): ?FileNode
    {
        $path = $this->cutArrayPath($info->getPath());
        if (is_null($path)) {
            return null;
        }

//print_r(['info' => $info, 'path' => $pathToCut, 'cut' => $path, 'dir' => $dir, 'name' => $name]);
        $node = new FileNode();
        $node->setData(
            $path,
            $info->getSize(),
            $info->getType(),
            true,
            true
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
