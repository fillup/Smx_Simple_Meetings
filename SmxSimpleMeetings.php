<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */


/**
 * This file handles all includes needed to use the SmxSimpleMeetings library.
 * If you are already using an autoloader you can configure to it to find 
 * classes in the vendor/ folder.
 */


/**
 * Base classes are required for all service providers
 */
require_once 'adapters/Smx/SimpleMeetings/Interfaces/Account.php';
require_once 'adapters/Smx/SimpleMeetings/Interfaces/Meeting.php';
require_once 'adapters/Smx/SimpleMeetings/Interfaces/User.php';
require_once 'adapters/Smx/SimpleMeetings/Interfaces/Attendee.php';
require_once 'adapters/Smx/SimpleMeetings/Shared/Account.php';
require_once 'adapters/Smx/SimpleMeetings/Shared/ItemList.php';
require_once 'adapters/Smx/SimpleMeetings/Shared/HttpRequest.php';
require_once 'adapters/Smx/SimpleMeetings/Shared/Time.php';

// Removing dependency on Zend\Http for now due to problems with composer
/*if(!class_exists('\\Zend\\Http\\Client')){
    set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../../zendframework/zendframework/library/');
    require_once 'Zend/Loader/StandardAutoloader.php';
    $loader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
    $loader->register();
    require_once __DIR__.'/../../zendframework/zendframework/library/Zend/Http/Client.php';
}*/

/**
 * Factory class is required
 */
require_once 'adapters/Smx/SimpleMeetings/Factory.php';

/**
 * WebEx Classes
 * 
 * This section can be commented out if you do not plan to use WebEx.
 */
require_once 'adapters/Smx/SimpleMeetings/WebEx/Account.php';
require_once 'adapters/Smx/SimpleMeetings/WebEx/Attendee.php';
require_once 'adapters/Smx/SimpleMeetings/WebEx/Meeting.php';
require_once 'adapters/Smx/SimpleMeetings/WebEx/User.php';
require_once 'adapters/Smx/SimpleMeetings/WebEx/Utilities.php';

/**
 * Citrix Classes
 * 
 * This section can be commented out if you do not plan to use Citrix.
 */
require_once 'adapters/Smx/SimpleMeetings/Citrix/Account.php';
require_once 'adapters/Smx/SimpleMeetings/Citrix/Utilities.php';
require_once 'adapters/Smx/SimpleMeetings/Citrix/Meeting.php';
require_once 'adapters/Smx/SimpleMeetings/Citrix/Attendee.php';
require_once 'adapters/Smx/SimpleMeetings/Citrix/User.php';

/**
 * Join.Me Classes
 * 
 * This section can be commented out if you do not plan to use Join.Me
 */
require_once 'adapters/Smx/SimpleMeetings/JoinMe/Account.php';
require_once 'adapters/Smx/SimpleMeetings/JoinMe/Meeting.php';
require_once 'adapters/Smx/SimpleMeetings/JoinMe/Utilities.php';

/**
 * BigBlueButton Classes
 * 
 * This section can be commented out if you do not plan to use BigBlueButton
 */
require_once 'adapters/Smx/SimpleMeetings/BigBlueButton/Account.php';
require_once 'adapters/Smx/SimpleMeetings/BigBlueButton/Meeting.php';
require_once 'adapters/Smx/SimpleMeetings/BigBlueButton/Utilities.php';