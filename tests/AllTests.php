<?php

namespace Smx\SimpleMeetings\Tests;

require_once 'BaseClasses/AllTests.php';
require_once 'WebEx/AllTests.php';

class AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('SmxSimpleMeetings');
        
        $suite->addTest(\Smx\SimpleMeetings\Tests\Base\BaseClasses_AllTests::suite());
        $suite->addTest(\Smx\SimpleMeetings\Tests\WebEx\WebEx_AllTests::suite());
        
        return $suite;
    }
}
