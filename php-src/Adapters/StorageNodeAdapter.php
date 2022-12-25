<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_tree\Interfaces\ITree;


/**
 * Class StorageNodeAdapter
 * @package kalanis\kw_tree\Adapters
 * Create tree node from Storage record (I have little to no information about Node)
 * @todo: komplet vyhodit? - ma problemy s rozlisenim souboru a slozek, protoze to zavisi na samotnem storagi
 *        zaroven by zmizela jedna otravna zavislost

Normal file
path - the whole path against cutDir
 *
Normal dir
path - the whole path against cutDir
 *
Root dir for lookup is a bit different:
path - empty
 */
class StorageNodeAdapter
{
    /** @var IStorage */
    protected $storage = null;
    /** @var string */
    protected $dirDelimiter = IPaths::SPLITTER_SLASH;
    /** @var string[] */
    protected $cutDir = [];

    public function __construct(IStorage $storage, string $dirDelimiter = IPaths::SPLITTER_SLASH)
    {
        $this->storage = $storage;
        $this->dirDelimiter = $dirDelimiter;
    }

    /**
     * @param string[] $dir
     * @throws StorageException
     * @return $this
     */
    public function cutDir(array $dir): self
    {
        $path = implode($this->dirDelimiter, $dir);
        $this->cutDir = $this->storage->exists($path) ? $path + [''] : [];
        return $this;
    }

    /**
     * @param string $path
     * @throws FilesException
     * @throws StorageException
     * @return FileNode|null
     */
    public function process(string $path): ?FileNode
    {
        $data = $this->storage->read($path);
        if (is_resource($data)) {
            // copy stream to temporary one
            $resource = fopen('php://temp', 'rb+');
            rewind($data);
            $size = stream_copy_to_stream($data, $resource, -1, 0);
            if (false === $size) {
                // @codeCoverageIgnoreStart
                throw new StorageException('Cannot get size from resource');
            }
            // @codeCoverageIgnoreEnd
            if (200 > $size) {
                rewind($resource);
                $content = stream_get_contents($resource, -1, 0);
            } else {
                $content = 'just binary string';
            }
        } else {
            $content = strval($data);
            $size = strlen($content);
        }
        $cut = $this->cutArrayPath($path);
        if (is_null($cut)) {
            return null;
        }

        $node = new FileNode();
        $node->setData(
            $cut,
            $size,
            $this->getType($path, $content),
            true,
            true
        );
        return $node;
    }

    /**
     * @param string $path
     * @throws FilesException
     * @return string[]|null
     */
    protected function cutArrayPath(string $path): ?array
    {
        $arr = Stuff::pathToArray($path, $this->dirDelimiter);
        if (empty($this->cutDir)) {
            return $arr;
        }

        foreach ($this->cutDir as $pos => $cut) {
            if (!isset($arr[$pos])) {
                return null;
            }
            if ($arr[$pos] != $cut) {
                return null;
            }
        }

        return array_slice($arr, count($this->cutDir));
    }

    protected function getType(string $path, string $content): string
    {
        if ('' == $path) {
            return ITypes::TYPE_DIR;
        }
        if ($this->dirDelimiter == mb_substr($path, -1, 1)) {
            return ITypes::TYPE_DIR;
        }
        if (ITree::STORAGE_NODE_KEY == $content) {
            return ITypes::TYPE_DIR;
        }
        return ITypes::TYPE_FILE;
    }
}
