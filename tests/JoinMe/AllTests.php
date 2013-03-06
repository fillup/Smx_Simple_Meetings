<?php

namespace Smx\SimpleMeetings\Tests\JoinMe;

require_once 'AccountTest.php';
require_once 'MeetingTest.php';

class JoinMe_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('SmxSimpleMeetings JoinMe');
        
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\JoinMe\\AccountTest');
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\JoinMe\\MeetingTest');
        
        return $suite;
        
    }
}
