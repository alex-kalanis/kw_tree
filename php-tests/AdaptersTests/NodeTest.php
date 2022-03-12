<?php

namespace AdaptersTests;


use kalanis\kw_tree\Adapters\NodeAdapter;
use kalanis\kw_tree\Interfaces\ITree;


class NodeTest extends \CommonTestClass
{
    /**
     * @param string $path
     * @param string $dir
     * @param string $name
     * @param string $type
     * @param bool $isFile
     * @param bool $isDir
     * @dataProvider pathsProvider
     */
    public function testLinks(string $path, string $dir, string $name, string $type, bool $isFile, bool $isDir): void
    {
        $src = new \SplFileInfo($path);
        $lib = new NodeAdapter();
        $lib->cutDir($this->getSysDir());
        $node = $lib->process($src);
        if (
            (DIRECTORY_SEPARATOR != $name) // you cannot compare root directory path without realpath()
            && ('.' != mb_substr($path, -1)) // cou cannot compare dir represented with dot (as current)
        ) {
            $this->assertEquals($path, $this->getSysDir() . $dir . $name);
        }
        $this->assertEquals($name, $node->getName());
        $this->assertEquals($dir, $node->getDir());
        $this->assertEquals($type, $node->getType());
        $this->assertEquals($isFile, $node->isFile());
        $this->assertEquals($isDir, $node->isDir());
    }

    public function pathsProvider(): array
    {
        return [
            [$this->getSysDir() . 'other1.txt', '', 'other1.txt', ITree::TYPE_FILE, true, false],
            [$this->getSysDir() . 'sub' . DIRECTORY_SEPARATOR . 'dummy3.txt', 'sub' . DIRECTORY_SEPARATOR, 'dummy3.txt', ITree::TYPE_FILE, true, false],
            [$this->getSysDir() . 'next_one', '', 'next_one', ITree::TYPE_DIR, false, true],
            [$this->getSysDir() . 'next_one' . DIRECTORY_SEPARATOR . '.', '', 'next_one', ITree::TYPE_DIR, false, true],
            [$this->getSysDir() . 'next_one' . DIRECTORY_SEPARATOR . 'sub_one' . DIRECTORY_SEPARATOR . '.', 'next_one' . DIRECTORY_SEPARATOR, 'sub_one', ITree::TYPE_DIR, false, true],
            [$this->getSysDir() . 'next_one' . DIRECTORY_SEPARATOR . 'sub_one', 'next_one' . DIRECTORY_SEPARATOR, 'sub_one', ITree::TYPE_DIR, false, true],
            [$this->getSysDir(), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, ITree::TYPE_DIR, false, true],
        ];
    }

    protected function getSysDir(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'tree' . DIRECTORY_SEPARATOR;
    }
}
