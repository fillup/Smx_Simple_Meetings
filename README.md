smx/simplemeetings
===================

## Brief Description ##
Class library and abstraction layer for integrating with web meetings providers such as WebEx and Citrix.

## Table Of Contents ##
   * [Purpose](#section_Purpose)
   * [Service Providers](#section_ServiceProviders)
   * [Improvement Ideas / TODO](#section_Todo)
   * [Installation](#section_Installation)
      * [Using Composer](#section_Composer)
      * [GitHub / Cloning Repository](#section_Cloning)
      * [Archive Download](#section_Download)
   * [Usage](#section_Usage)
      * [Simple Example](#section_SimpleExample)
   * [Citrix Notes](#section_citrix)
   * [Join.Me Notes](#section_joinme)
   * [Feedback / Support](#section_Feedback)

<a name='section_Purpose'></a>
## Purpose ##
The purpose of the SmxSimple_Meetings library is to provide a simple and consistent interface for interacting with any web meetings service provider. Using a Factory/Adapter design pattern the library can be extended to support any number of service providers, yet consumers of the library will only need to write their code once and with a simple configuration change be able to switch between any supported service providers.

<a name='section_ServiceProviders'></a>
## Service Providers ##
Below is the initial list of service providers we intend to support with this library. It is our hope that by developing this library as open source that additional service providers or developers will extend the functionality.

1. WebEx Meeting Center [COMPLETE]
2. Citrix GoToMeeting [90% Complete - Missing create/edit user features. Need an admin account to dev/test with.]
3. BigBlueButton [COMPLETE]
4. Join.Me [COMPLETE] - Note that this is a very basic service with limited functionality and an even more limited API.

<a name='section_Todo'></a>
## Improvement Ideas / TODO ##

1. Create Smx\SimpleMeetings\Exception to create consistent error reporting and exception interface. Primarily to ensure errors from service providers are translated and returned to developer consistently.
2. ~~Create Smx\SimpleMeetings\Base\Time class to create consistent/standard way to handle dates internally and require each service provider to convert them to proper format.~~ Added \Smx\SimpleMeetings\Shared\Time utility class for getting current local timestamp in UTC and it is now expected that times be unixtimestamps in objects and then converted to proper string formatting in each adapter as necessary for each API.
3. Need another developer to perform a code review and help identify areas of inconsistency.

<a name='section_Installation'></a>
## Installation ##
You have at least three options for how to instal Smx\SimpleMeetings. The first and preferred method is to use Composer. The second option is to just clone the Git repository. The third, old school method, is to just download an archive of the source.

<a name='section_Composer'></a>
### Installation - Using Composer ###
If you have never used composer, check it out at http://getcomposer.org/. It provides a simple way for you to define your requirements, run a command, and have it automatically download and install any dependencies.
Since the installation of Composer is so simple, I'll provide the full instructions of installing Composer and using it to install Smx\SimpleMeetings.
Installing Composer, from the root directory of your project, run:
````
curl -s https://getcomposer.org/installer | php
````
This will create a composer.phar and vendor/ folder.
Next, create a composer.json file with contents:
````json
{
    "require": {
        "smx/simplemeetings": "dev-master"
    }
}
````
Next, run the following command to tell composer to install the package:
````
php composer.phar install
````
Now you'll have the folder vender/smx/simplemeetings/ with the library source inside. One of the great things about composer is it automatically inspects the tags from the Git repository and checks out the latest tag (if you use dev-master in your require statement).
To update the library you just run:
````
php composer.phar update
````

<a name='section_Cloning'></a>
### Installation - Using GitHub / Cloning the repository ###
If you dont want to use Composer, you can simply clone the repository. If you are not already using Git for source control management, you really should be, you can learn more about it and how to install it at http://git-scm.com/.
When cloning the repository you want to also make sure to checkout the latest tag to ensure you have a stable copy:
````
$ git clone git://github.com/fillup/Smx_Simple_Meetings.git
Cloning into 'Smx_Simple_Meetings'...
...
Resolving deltas: 100% (84/84), done.
$ cd Smx_Simple_Meetings/
$ git tag -l
v0.1.0
v0.1.1
v0.1.2
$ git checkout v0.1.2
Note: checking out 'v0.1.2'.
...
HEAD is now at 46f9d40... 
````
To update the library with this method:
````
$ git pull
...
$ git tag -l
...
$ git checkout v#.#.#
...
````

<a name='section_Download'></a>
### Installation - Downloading Files ###
From the GitHub project homepage, browse to the latest Tag under the Branches button. When you're viewing the latest tag, click on the Zip button to download an archive of the source. Then simple unzip it to wherever you want.
To update the library using this method you'll need to download the latest tag again, unzip it, and then sync the files over top of the original ones you downloaded.

<a name='section_Usage'></a>
## Usage Instructions ##
The purpose for Smx\SimpleMeetings is to make interacting with web meetings service providers very easy and consitent.

At this time we only provide a single file for you to include and then you have access to all the features. We plan to also provide configurations for common autoloaders so that you dont need to include anything but rather just update our autoloader configuration. We also plan to update the composer descriptor to define the autoloader details to make it autoloader capabilities available to anyone using composer, regardless if they are also using another framework like Zend Framework or Yii.

<a name='section_SimpleExample'></a>
Simple example of how to schedule a WebEx meeting:
````php
<?php
require_once 'path/to/vendor/SmxSimpleMeetings.php';
$username = 'exampleuser';
$password = 'MyP@ss!';
/**
* Sitename is the webex subdomain your account is on, 
* like http://meet.webex.com/ (you can get a free account there)
*/
$sitename = 'companyname'; 
$meeting = Factory::SmxSimpleMeeting('WebEx', 'Meeting', 
                $username, $password, $sitename);
/*
* All meeting options are optional, except maybe 
* meetingPassword if your site requires a password.
*/
$meeting->createMeeting(array(
    'meetingPassword' => 'Comp123', 
    'meetingName'     => 'This is my meeting',
    'startTime'       => '03/01/2013 10:00:00',
    'duration'        => '60',
    'isPublic'        => true
));

echo "Meeting is scheduled, meeting key: " . $meeting->meetingKey;

````
More comprehensive API documentation is under development, but for now just read the Interfaces.php to understand what methods are available.

<a name='section_citrix'></a>
## Citrix Notes ##
Citrix uses oAuth for authencitation and uses an access token to authorize any API calls. If you've integrated with other oAuth providers then you're already familiar with the flow.

To call Citrix APIs you'll also need an API Key. You can get one of these by registering at http://developer.citrixonline.com/. Make sure your application URL is the same domain that you'll be hosting your application. For oAuth the user can only be redirected to URLs on the same domain.

Assuming you do not already know your user's access token:

1. Get new object from factory.
2. Get Auth URL
3. Redirect user to Auth URL
4. After user logs in and grants access to your application they will be redirected back to your site with a parameter ?code= which will be a responseKey
5. Get a new object from factory again and call the authForAccessToken method with the response key
6. Your object will now have the accessToken and organizerKey attributes set, you may want to store these to save the user from logging in again in the future, but be responsible of course.

Example:
````php
$authInfo = array(
    'apiKey' => 'YOUR_API_KEY_HERE'
);
$account = Factory::SmxSimpleMeeting('Citrix', 'Account', $authInfo);
$authUrl = $meeting->getAuthUrl();
// Redirect User to Auth URL
// ...
// User has returned with ?code=123456789
$responseKey = $_GET['code'];
$authInfo = array(
    'apiKey' => 'YOUR_API_KEY_HERE'
);
$account = Factory::SmxSimpleMeeting('Citrix', 'Account', $authInfo);
$account->authForAccessToken($responseKey);
echo 'Access token: '.$account->getAccessToken();
echo 'Organizer Key: '.$account->organizerKey;
// Future calls
$authInfo = array(
    'apiKey' => 'YOUR_API_KEY_HERE',
    'accessToken' => 'YOUR_ACCESS_TOKEN_HERE',
    'organizerKey' => 'YOUR_ORGANIZER_KEY_HERE'
);
$options = array(
    'meetingName' => 'My First Meeting'
);     
$meeting = Factory::SmxSimpleMeeting('Citrix', 'Meeting', $authInfo, $options);
$meeting->createMeeting();
````

<a name='section_joinme'></a>
## Join.Me Notes ##
Join.Me (http://join.me/) is a very simple and easy to use desktop sharing service. They also have an even more basic API to provide limited integration capabilities with their product. There are pretty much only two APIs:

1. Authenticate a user and get an authCode returned
2. Create a meeting/session and get a code and ticket to use to start it

You can checkout their APIs at http://help.join.me/knowledgebase/topics/26996-join-me-api

One thing to note is that their concept of an authCode is based at a user level, but not also at an app level. So if you generate an authCode for a user using this library, and then that user uses some other service that calls the same API, it will invalidate the previous authCode. So just something to be aware of.

<a name='section_Feedback'></a>
## Feedback / Support ##
Please use the GitHub ticket system to raise feature requests and bugs. We want this to be an extremenly easy yet flexible library for you to use so please let us know how we can improve on it. We would also love for anyone to join the project to help provide adapters for additional service providers and extend the ones we already have.
