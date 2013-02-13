<?php

namespace Smx\SimpleMeetings\Tests\Base;

require_once 'MeetingTest.php';


class BaseClasses_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('SmxSimpleMeetings BaseClasses');
        
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\Base\\MeetingTest');
        
        return $suite;
        
    }
}
