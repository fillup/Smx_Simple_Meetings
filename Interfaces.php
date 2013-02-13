<?php

namespace Smx\SimpleMeetings;

interface Site
{
    public function getSitename();
    public function getUsername();
    public function getPassword();
    public function setSitename($sitename);
    public function setUsername($username);
    public function setPassword($password);
}

interface Meeting
{
    public function createMeeting($options=false);
    public function getServerMeetingDetails();
    public function getMeetingList();
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
    public function setOptions($options);
    public function setOption($name,$value);
    public function getOption($name);
}

interface Attendee
{
    public function addAttendee();
    public function getAttendeeList();
}

interface User
{
    public function createUser();
    public function editUser();
    public function loginUser($urlOnly=false);
    public function getUserDetails();
    public function getUserList();
}
