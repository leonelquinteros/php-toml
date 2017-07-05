[![Packagist](https://img.shields.io/packagist/v/leonelquinteros/php-toml.svg)](https://packagist.org/packages/leonelquinteros/php-toml)
[![Packagist](https://img.shields.io/packagist/l/leonelquinteros/php-toml.svg)](https://packagist.org/packages/leonelquinteros/php-toml)
[![Build Status](https://travis-ci.org/leonelquinteros/php-toml.svg?branch=master)](https://travis-ci.org/leonelquinteros/php-toml)
[![Packagist](https://img.shields.io/packagist/dt/leonelquinteros/php-toml.svg)](https://packagist.org/packages/leonelquinteros/php-toml)


PHP TOML parser
===============

PHP parser for TOML language ( https://github.com/toml-lang/toml )


Support
-------

TOML version: [v0.4.0](https://github.com/mojombo/toml/blob/master/versions/en/toml-v0.4.0.md)


Requirements
------------

- PHP 5.2+


Installation
------------

Grab src/Toml.php and use it where you need it.

This library intends to stay simple and support older versions of PHP down to 5.2.
Common autoloaders would work for standard use as long as the file Toml.php is in the include path.


If you need to use it into namespaced environments or with a certain autoloader struct,
you can wrap it up to your own taste creating a simple file like:


##### /some/project/namespace/path/My/Own/Toml.php

```php
<?php
namespace My\Own

require("/path/to/github/cloned/repo/php-toml/src/Toml.php");

```


##### /some/project/autoloader/class/My/Own/Toml.php

```php
<?php

require("/path/to/github/cloned/repo/php-toml/src/Toml.php");

class My_Own_Toml extends Toml {}

```


Usage
-----

```php
<?php
require("../src/Toml.php");

// Parse TOML string
$tomlStr = file_get_contents('example.toml');
$result = Toml::parse($tomlStr);


// Parse TOML file path
$result = Toml::parseFile('example.toml');
```


Test
----

This project supports the toml-test Test Suite:

(https://github.com/BurntSushi/toml-test)

To run the test suite, after toml-test is installed on your environment, run the following:

```bash
toml-test /path/to/github/cloned/repo/php-toml/toml-test/test.php
```


Contribute
----------

- Use and test the lib.
- Report issues/bugs/comments/suggestions on Github
- Send me your pull requests with improvements/new features


License
-------

BSD License

```
Copyright (c) 2013 Leonel Quinteros.
All rights reserved.


 Redistribution and use in source and binary forms, with or without
 modification, are permitted provided that the following conditions are
 met:

 * Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above
   copyright notice, this list of conditions and the following disclaimer
   in the documentation and/or other materials provided with the
   distribution.
 * Neither the name of the  nor the names of its
   contributors may be used to endorse or promote products derived from
   this software without specific prior written permission.

 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

```
