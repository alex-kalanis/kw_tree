<?php

namespace kalanis\kw_tree;


use ArrayAccess;
use kalanis\kw_input\Interfaces\IVariables;


/**
 * Trait TWhereDir
 * @package kalanis\kw_tree
 * Trait to controllers to access dirs from session
 */
trait TWhereDir
{
    protected $whereConst = 'dir';
    /** @var ArrayAccess|null */
    protected $storeWhere = null;
    /** @var IVariables|null */
    protected $anotherSource = null;

    public function initWhereDir(ArrayAccess $storeWhere, ?IVariables $inputs): void
    {
        $this->storeWhere = $storeWhere;
        $this->anotherSource = $inputs;
    }

    public function updateWhereDir(string $where): void
    {
        if ($this->storeWhere) {
            $this->storeWhere->offsetSet($this->whereConst, $where);
        }
    }

    public function getWhereDir(): string
    {
        if ($this->storeWhere && $this->storeWhere->offsetExists($this->whereConst)) {
            return $this->storeWhere->offsetGet($this->whereConst);
        }
        if ($this->anotherSource) {
            $dirs = $this->anotherSource->getInArray($this->whereConst);
            return strval(reset($dirs));
        }
        return '';
    }
}
