<?php

namespace kalanis\kw_tree\Interfaces;


interface ITree
{
    const CURRENT_DIR = '.';
    const PARENT_DIR = '..';

    /** @link https://www.php.net/manual/en/splfileinfo.gettype.php */
    const TYPE_FILE = 'file';
    const TYPE_LINK = 'link';
    const TYPE_DIR = 'dir';
    const TYPE_BLOCK = 'block';
    const TYPE_FIFO = 'fifo';
    const TYPE_CHAR = 'char';
    const TYPE_SOCKET = 'socket';
    const TYPE_UNKNOWN = 'unknown';
}
