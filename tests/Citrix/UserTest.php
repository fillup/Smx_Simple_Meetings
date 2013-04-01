<?php
namespace Smx\SimpleMeetings\Tests\Citrix;

require_once __DIR__.'/../../vendor/autoload.php';

use Smx\SimpleMeetings\Factory;
use Smx\SimpleMeetings\Citrix\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    private $authInfo;
    
    protected function setUp()
    {
        parent::setUp();
        if(is_null($this->authInfo)){
            include __DIR__.'/../../config.local.php';
            $this->authInfo = array(
                'apiKey' => $CitrixAPIKey,
                'accessToken' => $CitrixAccessToken,
                'organizerKey' => $CitrixOrganizerKey
            );
        }
    }
    
    public function testGetServerUserDetailsByOrganizerKey()
    {
        $user = Factory::SmxSimpleMeeting('Citrix', 'User', $this->authInfo);
        $details = $user->getServerUserDetails($this->authInfo['organizerKey']);
        $this->assertTrue(is_object($details));
    }
}
