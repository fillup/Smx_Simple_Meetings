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
require_once 'Interfaces.php';
require_once 'adapters/BaseClasses/Site.php';
require_once 'adapters/BaseClasses/Meeting.php';
require_once 'adapters/BaseClasses/Attendee.php';
require_once 'adapters/BaseClasses/ItemList.php';
require_once 'adapters/BaseClasses/HttpRequest.php';

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
require_once 'adapters/Factory.php';

/**
 * WebEx classes.
 * This section can be commented out if you do not plan to use WebEx.
 */
require_once 'adapters/WebEx/Attendee.php';
require_once 'adapters/WebEx/Meeting.php';
require_once 'adapters/WebEx/User.php';
require_once 'adapters/WebEx/Utilities.php';