## AKQA Test - Booking for boardroom

Work Hours: 3 hours in 27 Sep., 6 hours in 28 Sep.


### Environment Requirement

Here aussme you have:

* Mac OS X (system version above 10.9) or Linux environment
* Installed PHP, version above 5.3.3

If not, install as following

* Mac OS X with system version above 10.9 have PHP support out of box. Check it from your terminal:
	
		php -v
		
* Linux, aussem you can use Ubuntu/Debian/Arch

		// for Ubuntu or Debian
		apt-get install php5-cli
		
		// for Arch
		pacman -S php


### Project

#### Structure

	/src/			// source code
	/tests/			// unit testing related
	/data.txt		// input source, the booking data
	/Run.php		// main file


#### Run

* go to root of this project folder
* execute following in your terminal, will see result output directly

		php Run.php
		
* you can change data from data.txt to retest

#### Unit Testing

* use https://phpunit.de/ as unit testing framework
* exceute in your terminal, will testing result output directly

		php phpunit.phar --bootstrap vendor/autoload.php tests/BookingTest.php 


### Known issues

1. date like 2/28, 2/29 and 2/30 not handle correctly yet
2. hours for meeting have to be interger
3. exception handler