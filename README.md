PHP Generics
==

The purpose of this package is to provide some classes for more sophisticated access to ressources. It can be used in any framework or application which intends to use abstract interfaces.

Currently it provides a basic stream API, a socket provider and a logging infrastructure based on the PSR-3. The intention is to extend it with more infrastructure code.

Usage
--

Please take a look into API documentation. To create the API documentation by yourself, please install composer and execute

	composer install
	composer documentation

	
Stability
--

The API is a test-driven framework and uses PHPUnit to test the stability. Please install composer (if not yet done, see Usage) and execute

	composer test
	
Feel free to write and publish further tests.

In case of an error please report bugs using a PHPUnit test class. Take a look into the tests/stream-tests folder to see example how to do create a new one.


License
--

The whole package is published under the terms of second BSD License (BSD2). Take a look into LICENSE.md


Pointers
--
This framework makes use of composer available at https://getcomposer.org/ to generate API documentation and perform tests.