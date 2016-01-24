[![Build Status](https://travis-ci.org/maikgreubel/phpgenerics.svg?branch=master)](https://travis-ci.org/maikgreubel/phpgenerics)
[![Code Coverage](https://scrutinizer-ci.com/g/maikgreubel/phpgenerics/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/maikgreubel/phpgenerics/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/maikgreubel/phpgenerics/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/maikgreubel/phpgenerics/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/55e2f9bec6d8f2001d000350/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55e2f9bec6d8f2001d000350)

PHP Generics
==

The purpose of this package is to provide some classes for more sophisticated access to ressources. It can be used in any framework or application which intends to use abstract interfaces.

Currently it provides a basic stream API, a socket provider and a logging infrastructure based on the PSR-3. The intention is to extend it with more infrastructure code.

Usage
--

Please take a look into API documentation. To create the API documentation by yourself, please install Apache Ant and execute

	ant
	
Any further execution can be performed without updating the dependencies using command

	ant no-update

	
Stability
--

The API is a test-driven framework and uses PHPUnit to test the stability. Please use ant (if not yet done, see Usage) and execute

	ant
	
This will run all available test suites in tests/*. The command performs also some basic checks for copy&paste code parts and calculate the code quality.
	
Feel free to write and publish further tests.

In case of an error please report bugs using a PHPUnit test class. Take a look into the tests/ sub folders to see examples how to do create a new one.


License
--

The whole package is published under the terms of second BSD License (BSD2). Take a look into LICENSE.md


Pointers
--
This framework makes use of composer available at https://getcomposer.org/ to generate API documentation and perform tests.
