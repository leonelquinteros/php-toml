PHP TOML parser
===============

PHP parser for TOML language ( https://github.com/mojombo/toml )

Datetime values are converted to UNIX time using strtotime(). Deciding what to do with them.

Supports commit: 751cec15d8f183b15cab3d92b6a7faa145316a13 ( 2013-03-17 )


Requirements
------------

- PHP 5.2+


Installation
------------

Grab src/toml.php and put it where you need it.


Usage
-----

```
<?php
require("../src/toml.php");

// Parse TOML string
$tomlStr = file_get_contents('example.toml');
$result = Toml::parse($tomlStr);


// Parse TOML file path
$result = Toml::parseFile('example.toml');
```


Contribute
----------

- Use and test the lib.
- Report issues/bugs/comments/suggestions on Github
- Send me your pull requests with descriptions of modifications/new features



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
