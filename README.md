# ProcessWire Tests #

This aims to be a test suite for ProcessWire core, especially for the selector engine. Tests are built using PHPUnit and are meant to be run against a clean installation of bleeding edge ProcessWire (dev branch that is) with SkyscraperProfile.

The idea is to run the same selectors against both database and an in-memory PageArray consisting of all pages retrieved from the database. Both ways we should end up with the same set of data, thus same assertions should hold true. This aims making and keeping the API variants consistent (db and in-memory) - and of course free of bugs.

Current version runs some tests with simple selectors and different kinds of operators. Later on there will (hopefully) be code coverage metrics included as well as a whole lot more tests.


## Setting up the environment ##

### Prerequisites ###

In addition to requirements of ProcessWire itself, the tests need a working installation of PHPUnit. To install PEAR and PHPUnit on a Mac these resources have been found useful:

* [PEAR and PECL on Mac OS X](http://jason.pureconcepts.net/2012/10/install-pear-pecl-mac-os-x/)
* [PEAR, PHPUnit and XDebug on Mac OS X 10.6](http://www.newmediacampaigns.com/page/install-pear-phpunit-xdebug-on-macosx-snow-leopard)
* [Install PEAR on MAMP](http://stackoverflow.com/questions/5510734/install-pear-on-mamp)

### ProcessWire installation ###

See [SkyscrapersProfile](https://github.com/ryancramerdesign/SkyscrapersProfile) for instructions on installing ProcessWire with that profile.

### The Test Suite ###

Clone [ProcessWireTests](https://github.com/niklaka/ProcessWireTests.git) under clean PW + Skyscrapres installation, for eaxample into a directory *tests*.


## Running the tests ##

If the environment is properly set up, running the tests is as simple as:
<pre>phpunit tests</pre>

------
Copyright (c) 2013 Niklas Lakanen