<?php

namespace AdaptersTests;


use kalanis\kw_tree\Adapters\NodeAdapter;
use kalanis\kw_tree\Interfaces\ITree;


class NodeTest extends \CommonTestClass
{
    /**
     * @param string $path
     * @param string $name
     * @param string $dir
     * @param string $type
     * @param bool $isFile
     * @param bool $isDir
     * @dataProvider pathsProvider
     */
    public function testLinks(string $path, string $name, string $dir, string $type, bool $isFile, bool $isDir): void
    {
        $src = new \SplFileInfo($path);
        $lib = new NodeAdapter();
        $lib->cutDir($this->getSysDir());
        $node = $lib->process($src);
        $this->assertEquals($name, $node->getName());
        $this->assertEquals($dir, $node->getDir());
        $this->assertEquals($type, $node->getType());
        $this->assertEquals($isFile, $node->isFile());
        $this->assertEquals($isDir, $node->isDir());
    }

    public function pathsProvider(): array
    {
        return [
            [$this->getSysDir() . 'tree' . DIRECTORY_SEPARATOR . 'other1.txt', 'other1.txt', DIRECTORY_SEPARATOR. 'tree', ITree::TYPE_FILE, true, false],
            [$this->getSysDir() . 'tree' . DIRECTORY_SEPARATOR . 'next_one', 'next_one', DIRECTORY_SEPARATOR . 'tree', ITree::TYPE_DIR, false, true],
            [$this->getSysDir() . 'tree' . DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . '.', 'sub', DIRECTORY_SEPARATOR . 'tree', ITree::TYPE_DIR, false, true],
            [$this->getSysDir(), 'data', DIRECTORY_SEPARATOR, ITree::TYPE_DIR, false, true],
        ];
    }

    protected function getSysDir(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
    }
}
