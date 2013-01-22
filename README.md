# ProcessWire Tests #

This aims to be a test suite for ProcessWire core, especially for the selector engine. Tests are built using PHPUnit and are meant to be run against a clean installation of bleeding edge ProcessWire (dev branch that is) with SkyscraperProfile.

The idea is to run the same selectors against both database and an in-memory PageArray consisting of all pages retrieved from the database. Both ways we should end up with the same set of data, thus same assertions should hold true. This aims making and keeping the API variants consistent (db and in-memory) - and of course free of bugs.

Current version runs some tests with simple selectors having only basic operators. Later on there will (hopefully) be code coverage metrics included as well as a whole lot more tests.


## Setting up the environment ##

### Prerequisites ###

In addition to requirements of ProcessWire itself, the tests need a working installation of PHPUnit. To install PEAR and PHPUnit on a Mac follow these few steps (tried out on OS X Snow Leopard / Lion):

1. Download PEAR and start installation
<pre>
	mkdir /tmp/pear
	cd /tmp/pear
	curl -O http://pear.php.net/go-pear.phar
	sudo php -d detect_unicode=0 go-pear.phar
</pre>
2. Configure PEAR
  * Change "Installation base" (1) to <pre>/usr/local/pear</pre>
  * Change "Binaries directory" to <pre>/usr/local/bin</pre>

3. Install PHPUnit using PEAR
<pre>
	sudo pear config-set auto_discover 1
	sudo pear install pear.phpunit.de/PHPUnit
</pre>

### ProcessWire installation ###

See [SkyscrapersProfile](https://github.com/ryancramerdesign/SkyscrapersProfile) for instructions on installing ProcessWire with that profile.

### The Test Suite ###

Clone [ProcessWireTests](https://github.com/niklaka/ProcessWireTests.git) under clean PW + Skyscrapres installation, for eaxample into a directory *tests*.


## Running the tests ##

If the environment is properly set up, running the tests is as simple as:
<pre>phpunit tests</pre>

------
Copyright (c) 2013 Niklas Lakanen