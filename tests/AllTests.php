<?php

namespace Smx\SimpleMeetings\Tests;

require_once 'Shared/AllTests.php';
require_once 'WebEx/AllTests.php';
require_once 'Citrix/AllTests.php';
require_once 'JoinMe/AllTests.php';
require_once 'BigBlueButton/AllTests.php';

class AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('SmxSimpleMeetings');
        
        $suite->addTest(\Smx\SimpleMeetings\Tests\Shared\Shared_AllTests::suite());
        $suite->addTest(\Smx\SimpleMeetings\Tests\WebEx\WebEx_AllTests::suite());
        $suite->addTest(\Smx\SimpleMeetings\Tests\Citrix\Citrix_AllTests::suite());
        $suite->addTest(\Smx\SimpleMeetings\Tests\JoinMe\JoinMe_AllTests::suite());
        $suite->addTest(\Smx\SimpleMeetings\Tests\BigBlueButton\BigBlueButton_AllTests::suite());
        
        return $suite;
    }
}
