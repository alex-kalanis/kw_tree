<?php

namespace BasicTests;


use kalanis\kw_paths\Path;
use kalanis\kw_tree\FileNode;
use kalanis\kw_tree\Filters\DirFilter;
use kalanis\kw_tree\Tree;


class TreeTest extends \CommonTestClass
{
    public function testTree(): void
    {
        $paths = new Path();
        $paths->setDocumentRoot(__DIR__ . '/../data'); // system root - where are all files
        $lib = new Tree($paths);
        $lib->startFromPath('tree'); // user's current dir to scan
        $lib->canRecursive(false);
        $lib->process();
        $this->assertNotEmpty($lib->getTree());

        $lib->canRecursive(true);
        $lib->process();
        $this->assertNotEmpty($lib->getTree());
        $this->assertNotEmpty($lib->getTree());

        $filter = new DirFilter();
        $filtered = $filter->filter($lib->getTree());
        $this->assertNotEmpty($filtered);
        $this->assertEquals(4, count($filtered->getSubNodes()));

        $lib->setFilterCallback([$this, 'fileCallback']);
        $lib->canRecursive(false);
        $lib->process();
        $tree = $lib->getTree();
        $this->assertEquals(4, count($tree->getSubNodes()));
    }

    public function fileCallback(\SplFileInfo $file): bool
    {
        return $file->isFile();
    }

    public function testEmptyFilter(): void
    {
        $source = new FileNode();
        $filter = new DirFilter();
        $filtered = $filter->filter($source);
        $this->assertEmpty($filtered);
    }
}
