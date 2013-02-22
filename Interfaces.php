<?php
/**
 * Smx\SimpleMeetings (https://github.com/fillup/Smx_Simple_Meetings/)
 *
 * @link      https://github.com/fillup/Smx_Simple_Meetings for the canonical source repository
 * @copyright Copyright (c) 2012-2013 Sumilux Technologies (http://sumilux.com)
 * @license   GPLv2+
 */

namespace Smx\SimpleMeetings;

interface Account
{
    public function __construct($authInfo);
    public function getAuthInfo();
    public function setAuthInfo($authInfo);
    public function getAuthType();
    public function setAuthType($authType);
    public function isAuthenticated();
}

interface Meeting
{
    public function createMeeting($options=false);
    public function getServerMeetingDetails();
    public function getMeetingList($options=false);
    public function startMeeting($urlOnly=false);
    public function joinMeeting($urlOnly=false,$attendeeName=false,
            $attendeeEmail=false,$meetingPassword=false);
    public function editMeeting($options=false);
    public function deleteMeeting();
    public function getActiveMeetings();
    public function getRecordingList($options=false);
    public function addAttendee($name, $email, $sendInvite=false);
    public function getAttendeeList();
    public function getMeetingHistory();
    public function getAttendeeHistory();
}

interface Attendee
{
    public function addAttendee($options=false);
    public function getAttendeeList();
}

interface User
{
    public function createUser($options=false);
    public function editUser($options=false);
    public function loginUser($urlOnly=false);
    public function getServerUserDetails($username=false);
    public function getUserList($options=false);
    public function deactivate($username=false);
}
