<?php
namespace Codeception\Module;

use AspectMock\Test;
use Codeception\Module;
use Codeception\TestCase;

class UnitHelper extends \Codeception\Module
{

    /**
     * @param TestCase $testcase
     */
    public function _after(TestCase $testcase)
    {
        Test::clean();
        parent::_after($testcase);
    }
}
