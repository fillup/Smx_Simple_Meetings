<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings\Interfaces;

interface User
{
    public function createUser($options=false);
    public function editUser($options=false);
    public function loginUser($urlOnly=false);
    public function getServerUserDetails($username=false);
    public function getUserList($options=false);
    public function deactivate($username=false);
}