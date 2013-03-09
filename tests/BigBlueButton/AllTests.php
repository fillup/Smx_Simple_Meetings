<?php

namespace Smx\SimpleMeetings\Tests\BigBlueButton;

require_once 'AccountTest.php';
require_once 'MeetingTest.php';

class BigBlueButton_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('SmxSimpleMeetings BigBlueButton');
        
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\BigBlueButton\\AccountTest');
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\BigBlueButton\\MeetingTest');
        
        return $suite;
        
    }
}
