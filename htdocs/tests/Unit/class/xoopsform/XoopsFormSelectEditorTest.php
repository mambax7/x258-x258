<?php

declare(strict_types=1);

//namespace Xoops\Tests\Database;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/include/functions.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/simpleform.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formelement.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsform/formselecteditor.php');

class XoopsFormSelectEditorTest extends TestCase
{
    protected $myClass = 'XoopsFormSelectEditor';

    public function test___construct()
    {
        $form     = new XoopsSimpleForm('title', 'name', 'action');
        $instance = new $this->myClass($form);
        $this->assertInstanceOf('XoopsFormSelectEditor', $instance);
    }
}
