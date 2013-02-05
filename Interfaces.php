<?php

namespace Smx\SimpleMeetings;

interface Meeting
{
    public function createMeeting();
    public function getMeetingDetails();
    public function getMeetingList();
    public function startMeeting($urlOnly=false);
    public function joinMeeting($urlOnly=false);
    public function editMeeting();
    public function deleteMeeting();
    public function getActiveMeetings();
    public function getRecordingList();
    public function addAttendee();
    public function getAttendeeList();
    public function getMeetingHistory();
    public function getAttendeeHistory();
    public function setOptions($options);
    public function setOption($name,$value);
    public function getOption($name);
}

interface MeetingList extends \Iterator
{
    public function getArray();
}

interface Attendee
{
    public function addAttendee();
    public function getAttendeeList();
}

interface AttendeeList extends \Iterator
{
    public function getArray();
}

interface User
{
    public function createUser();
    public function editUser();
    public function loginUser($urlOnly=false);
    public function getUserDetails();
    public function getUserList();
}

interface UserList extends \Iterator
{
    public function getArray();
}