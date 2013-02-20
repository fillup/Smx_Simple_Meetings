<?php

namespace Smx\SimpleMeetings\Tests\Shared;

require_once 'AccountTest.php';


class Shared_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('SmxSimpleMeetings Shared');
        
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\Shared\\AccountTest');
        
        return $suite;
        
    }
}
