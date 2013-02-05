Smx_Simple_Meetings
===================

## Brief Description ##
Class library and abstraction layer for integrating with web meetings providers such as WebEx and Citrix.

## Purpose ##
The purpose of the SmxSimple_Meetings library is to provide a simple and consistent interface for interacting with any web meetings service provider. Using a Factory/Adapter design pattern the library can be extended to support any number of service providers, yet consumers of the library will only need to write their code once and with a simple configuration change be able to switch between any supported service providers.

## Service Providers ##
Below is the initial list of service providers we intend to support with this library. It is our hope that by developing this library as open source that additional service providers or developers will extend the functionality.

1. WebEx Meeting Center
2. Citrix GoToMeeting
3. BigBlueButton
