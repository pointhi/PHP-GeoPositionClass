PHP-GeoPositionClass
====================

A simple PHP-Class which convert different Geographical Coordinate Systems

Overview
--------

GeoPosition.class.php is a PHP Class to convert GeoData like Coordinates. The Data-Input-Handling is very fault tolerant.

The Class can handle Coordinates in this styles:
+ Locator System			(e.g. JN67VU) or (e.g. AL45dw89ab18ad)
+ Coordinate Decimal        (e.g. +48.548621) or (e.g. 115,486545 E)
+ Coordinate Sexagesimal    (e.g. 15°15.52'N) or (e.g. +50°48'13,1234'')

Working with the Class
----------------------

+ As first you create an Object from Type "GeoPosition"
+ Then you can give the Class Coordinates with the Set-Functions
 + SetString($String) is a universal input Function for all supported Data-formats
+ Now You can get the Coordinates in a new Format with the Get-Functions

Info for correct working
------------------------

+ Input Data with SetString($String)

	When you input the data in one string note that the first coordinate is the Latitude and the second is the Longitude.
	The Coordinate must be in the Style "48.00 N 15.12 W" and not "N 48.00 W 15.12 ".
	Between Latitude and Longitude must be at least one space.
	The Spaces can be " ", or the HTML-Space "&nbsp;".
