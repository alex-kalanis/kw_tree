<?php

namespace BasicTests;


use kalanis\kw_input\Inputs;
use kalanis\kw_input\Sources\Basic;
use kalanis\kw_input\Variables;
use kalanis\kw_tree\TWhereDir;


class WhereTest extends \CommonTestClass
{
    public function testSubNodes(): void
    {
        $lib = new WhereDir();
        // nothing set, nothing get
        $lib->initWhereDir(new \ArrayObject(), null);
        $this->assertEquals('', $lib->getWhereDir());

        // from init - load data from system basics
        $source = new Basic();
        $source->setExternal([
            'dir' => 'qaywsxedc/rfv/tgbznh',
        ]);
        $inputs = new Inputs();
        $inputs->setSource($source);
        $inputs->loadEntries();

        // now check externally
        $lib->initWhereDir(new \ArrayObject(), new Variables($inputs));
        $this->assertEquals('qaywsxedc/rfv/tgbznh', $lib->getWhereDir());

        // own values
        $lib->updateWhereDir('ijnuhb/zgvftc');
        $this->assertEquals('ijnuhb/zgvftc', $lib->getWhereDir());
    }
}


class WhereDir
{
    use TWhereDir;
}
