<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_files\Node;
use kalanis\kw_paths\Stuff;
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
     * @param string $dir
     * @throws FilesException
     * @return $this
     */
    public function cutDir(string $dir): self
    {
        $path = Stuff::pathToArray($dir);
        if ($this->nodeProcessor->exists($path) && $this->nodeProcessor->isDir($path)) {
            $this->cutDir = $path;
        }
        return $this;
    }

    public function process(Node $info): FileNode
    {
//print_r(['info' => $info, 'path' => $pathToCut, 'cut' => $path, 'dir' => $dir, 'name' => $name]);
        $node = new FileNode();
        $node->setData(
            $this->clearPath($info),
            $info->getSize(),
            $info->getType(),
            true,
            true
        );
        return $node;
    }

    protected function shortRealPath(Node $info): string
    {
        $path = Stuff::arrayToPath($info->getPath());
        return $info->isDir() && (false === mb_strpos($path, $this->cutDir))
            ? Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR
            : $path
        ;
    }

    /**
     * @param Node $info
     * @return string[]
     */
    protected function clearPath(Node $info): array
    {
        $withoutProblems = array_filter(array_filter($info->getPath(), ['\kalanis\kw_paths\Stuff', 'notDots']));
        // if equals cut them out and return the rest from the name
        $pathToCut = array_slice($withoutProblems, 0, count($this->cutDir));
        if ($pathToCut == $this->cutDir) {
            $withoutProblems = array_slice($withoutProblems, count($this->cutDir));
        }
        return $withoutProblems;
    }
}
