<?php

namespace Smx\SimpleMeetings\Tests\Citrix;

require_once 'AccountTest.php';
require_once 'MeetingTest.php';
require_once 'UserTest.php';

class Citrix_AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('SmxSimpleMeetings Citrix');
        
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\Citrix\\AccountTest');
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\Citrix\\MeetingTest');
        $suite->addTestSuite('\\Smx\\SimpleMeetings\\Tests\\Citrix\\UserTest');
        
        return $suite;
        
    }
}
