<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Interfaces;

interface Account
{
    public function __construct($authInfo);
    public function getAuthInfo();
    public function setAuthInfo($authInfo);
    public function getAuthType();
    public function setAuthType($authType);
    public function isAuthenticated();
}