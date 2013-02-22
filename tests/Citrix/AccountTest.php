<?php
namespace Smx\SimpleMeetings\Tests\Citrix;

require_once __DIR__.'/../../SmxSimpleMeetings.php';

use Smx\SimpleMeetings\Factory;

class AccountTest extends \PHPUnit_Framework_TestCase
{
    private $authInfo;
    
    public function setUp() {
        parent::setUp();
        if(is_null($this->authInfo)){
            include __DIR__.'/../../config.local.php';
            $this->authInfo = array(
                'apiKey' => $CitrixAPIKey,
                'accessToken' => $CitrixAccessToken
            );
        }
    }
    
    public function testGetAuthUrl()
    {
        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Account', $this->authInfo);
        
        $authUrl = $meeting->getAuthUrl();
        $this->assertStringStartsWith('http', $authUrl);
    }
    
    /**
     * This test is commented out because it requires a one time use responseKey
     * that must be obtained manually via a browser.
     */
//    public function testAuthForAccessToken()
//    {
//        /*
//         * Each $responseKey can only be used once and must be retrieved via
//         * a browser after accessing the $authUrl, logging in, and authorizing
//         * your application. You'll be redirected back to your site with a 
//         * parameter ?code= followed by a requestKey. Plug that key into this
//         * test to verify if it works.
//         */
//        $responseKey = '';
//        $meeting = Factory::SmxSimpleMeeting('Citrix', 'Account', $this->authInfo);
//        $meeting->authForAccessToken($responseKey);
//        echo $meeting->accessToken;
//        $this->assertNotEmpty($meeting->accessToken);
//    }
}
