<?php

namespace Smx\SimpleMeetings;

interface Session
{
    public function createSession();
    public function getSessionDetails();
    public function getSessionList();
    public function startSession($urlOnly=false);
    public function joinSession($urlOnly=false);
    public function editSession();
    public function deleteSession();
    public function getActiveSessions();
    public function getRecordingList();
    public function addAttendee();
    public function getAttendeeList();
    public function getSessionHistory();
    public function getAttendeeHistory();
    public function setOptions($options);
    public function setOption($name,$value);
    public function getOption($name);
}

interface SessionList extends \Iterator
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