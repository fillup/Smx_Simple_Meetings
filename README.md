Smx_Simple_Meetings
===================

## Brief Description ##
Class library and abstraction layer for integrating with web meetings providers such as WebEx and Citrix.

## Purpose ##
The purpose of the SmxSimple_Meetings library is to provide a simple and consistent interface for interacting with any web meetings service provider. Using a Factory/Adapter design pattern the library can be extended to support any number of service providers, yet consumers of the library will only need to write their code once and with a simple configuration change be able to switch between any supported service providers.

## Service Providers ##
Below is the initial list of service providers we intend to support with this library. It is our hope that by developing this library as open source that additional service providers or developers will extend the functionality.

1. WebEx Meeting Center [COMPLETE]
2. Citrix GoToMeeting [NOT STARTED]
3. BigBlueButton [NOT STARTED]

## Improvement Ideas / TODO ##

1. Create Smx\SimpleMeetings\Exception to create consistent error reporting and exception interface. Primarily to ensure errors from service providers are translated and returned to developer consistently.
2. Create Smx\SimpleMeetings\Base\Time class to create consistent/standard way to handle dates internally and require each service provider to convert them to proper format.

## Installation ##
You have at least three options for how to instal Smx\SimpleMeetings. The first and preferred method is to use Composer. The second option is to just clone the Git repository. The third, old school method, is to just download an archive of the source.

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

### Installation - Downloading Files ###
From the GitHub project homepage, browse to the latest Tag under the Branches button. When you're viewing the latest tag, click on the Zip button to download an archive of the source. Then simple unzip it to wherever you want.
To update the library using this method you'll need to download the latest tag again, unzip it, and then sync the files over top of the original ones you downloaded.

## Usage Instructions ##
The purpose for Smx\SimpleMeetings is to make interacting with web meetings service providers very easy and consitent.

At this time we only provide a single file for you to include and then you have access to all the features. We plan to also provide configurations for common autoloaders so that you dont need to include anything but rather just update our autoloader configuration. We also plan to update the composer descriptor to define the autoloader details to make it autoloader capabilities available to anyone using composer, regardless if they are also using another framework like Zend Framework or Yii.

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

## Feedback / Support ##
Please use the GitHub ticket system to raise feature requests and bugs. We want this to be an extremenly easy yet flexible library for you to use so please let us know how we can improve on it. We would also love for anyone to join the project to help provide adapters for additional service providers and extend the ones we already have.
