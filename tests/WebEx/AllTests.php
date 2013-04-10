<?php

namespace Smx\SimpleMeetings\Tests\WebEx;

require_once 'AccountTest.php';
require_once 'MeetingTest.php';
require_once 'AttendeeTest.php';
require_once 'UserTest.php';


class WebEx_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('SmxSimpleMeetings WebEx');
        
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\WebEx\\AccountTest');
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\WebEx\\MeetingTest');
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\WebEx\\AttendeeTest');
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\WebEx\\UserTest');
        
        return $suite;
        
    }
}
