[Runkit7](https://github.com/runkit7/runkit7): Independent fork of runkit for PHP 7.2+
======================================================================================

For all those things you.... probably shouldn't have been doing anyway.... but surely do!
__Supports PHP7.2-8.1!__ (function/method manipulation is recommended only for unit testing, but all other functionality works.)

- Function/method manipulation crashes in PHP 7.4+ when opcache is enabled (e.g. `opcache.enable_cli`) ([Issue #217](https://github.com/runkit7/runkit7/issues/217))

  Disabling opcache is the recommended workaround.
- This has been tested with php 8.2.0beta1

[![Build Status](https://secure.travis-ci.org/runkit7/runkit7.png?branch=master)](http://travis-ci.org/runkit7/runkit7)
[![Build Status (Windows)](https://ci.appveyor.com/api/projects/status/3jwsf76ge0yo8v74/branch/master?svg=true)](https://ci.appveyor.com/project/TysonAndre/runkit7/branch/master)

[Building and installing runkit7 in unix](#building-and-installing-runkit7-in-unix)

[Building and installing runkit7 in Windows](#building-and-installing-runkit7-in-windows)

This extension's documentation is available at [https://www.php.net/runkit7](https://www.php.net/runkit7).

Compatibility: PHP7.2 to PHP 8.1
--------------------------------

**See [runkit-api.php](./runkit-api.php) for the implemented functionality and method signatures.** New functionality was added to support usage with PHP7.

- This adds the ability to set return types (including nullable return types) on added/redefined functions.
- This adds the ability to set `declare(strict_types=1)` on added/redefined functions.

Superglobals work reliably when tested on web servers and tests.
Class and function manipulation is recommended only for unit tests.

- The `runkit.superglobal` ini setting works reliably in PHP 7.
- Manipulating user-defined (i.e. not builtin or part of extensions) functions and methods via `runkit7_method_*` and `runkit7_function_*` generally works, **but is recommended only in unit tests** (unlikely to crash, but will cause memory leaks)
- Manipulating built in functions may cause segmentation faults in rare cases.
  File a bug report if you see this.
  **Function and method manipulation is recommended only for debugging or unit tests, because of the possibility of crashes**.
  (Manipulating built in class methods is impossible/not supported)
- Adding default properties to classes doesn't work in php7, because of a change
  in the way PHP stores objects.
  Eventually, I plan to add `runkit_default_property_modify`, which will replace one default value with a different default property, keeping the number of properties the same.
  See the reasons for disabling property manipulation at [PROPERTY\_MANIPULATION.md](./PROPERTY_MANIPULATION.md)
  As a substitute, user code can do the following things:

  - rename (or add) `__construct` with `runkit7_method_rename`/`runkit7_method_add`,
    and create a new version of `__construct` that sets the properties, then calls the original constructor.
  - For getting/setting properties of **individual objects**, see [ReflectionProperty](https://secure.php.net/manual/en/class.reflectionproperty.php)
    `ReflectionProperty->setAccessible(true)` and `ReflectionProperty->setValue()`, etc.
- Modifying constants works for constants declared in different files, but does not work for constants within the same file.
  PHP7 inlines constants within the same file if they are guaranteed to have only one definition.
  Patching php-src and/or opcache to not inline constants (e.g. based on a php.ini setting) is possible, but hasn't been tried yet.
- Sandboxing (and `runkit_lint`) were removed.

The following contributions are welcome:

-   Pull requests with  PHP5 -> PHP7 code migration of functions
-   New test cases for features that no longer work in PHP7, or bug reports containing code crashing runkit7.
-   Issues for PHP language features that worked in PHP5, but no longer work in PHP7,
    for the implemented methods (`runkit7_function_*` and `runkit7_method_*`)
-   Fixes and documentation.

Other methods and corresponding tests are disabled/skipped because changes to php internals in php7 made them impractical.

This is runkit7 4.x. Use runkit7 2.x for PHP 7.1 support, or 1.x for PHP 7.0 support.

Examples
--------

The following mocking libraries work with runkit7.

- [![Build Status](https://travis-ci.org/runkit7/Timecop-PHP.svg?branch=master)](https://travis-ci.org/runkit7/Timecop-PHP) [timecop-PHP (Fork)](https://github.com/runkit7/Timecop-PHP), a time testing library inspired by the ruby timecop gem (requires `runkit.internal_override=1`, suggested only for unit tests)
- [![CI](https://github.com/tototoshi/staticmock/actions/workflows/ci.yml/badge.svg)](https://github.com/tototoshi/staticmock/actions/workflows/ci.yml) [staticmock](https://github.com/tototoshi/staticmock), a mockery-like DSL to replace static methods in tests.
- [![Build Status](https://travis-ci.org/runkit7/SimpleStaticMock.svg?branch=master)](https://travis-ci.org/runkit7/SimpleStaticMock) [SimpleStaticMock (Fork)](https://github.com/runkit7/SimpleStaticMock), a very simple class to mock static methods in unit tests. Unrelated to tototoshi/staticmock.
- [![Build Status](https://travis-ci.org/runkit7/TraceOn.svg?branch=master)](https://travis-ci.org/runkit7/TraceOn) [TraceOn (Fork)](https://github.com/runkit7/TraceOn), a simple PHP utility to trace(investigate) invocations of instance/static/global methods.

## PHP7 SPECIFIC DETAILS

### Bugs in runkit7

-   There are segmentation faults when manipulating internal functions
    (a.k.a. "runkit.internal_override=1")
    (when you rename/redefine/(copy?) internal functions, and call internal functions with user functions' implementation, or vice versa)
    (and when functions redefinitions aren't cleaned up)
    Many (but not all) of these crashes have been fixed.
-   There are reference counting bugs causing memory leaks.
    2 calls to `emalloc` have been temporarily replaced with calls to `pemalloc`
    so that tests would not crash during shutdown (and other reasons)
-   The Zend VM bytecode representation may change in 8.0 betas and in future minor/major PHP releases after 8.0.
    Any new opcodes added may not work as expected with each new minor php version release.

### APIs for PHP7

#### Implemented APIs for PHP7

NOTE: Most `runkit7_*()` functions have aliases of `runkit_*()`.

-   `runkit7_function_*`: Most tests are passing. There are some memory leaks when renaming internal functions.
-   `runkit7_method_*`: Most tests are passing. There are some memory leaks when renaming internal functions.
-   `runkit7_zval_inspect`: Partly passing, and needs to be rewritten because of PHP7's zval changes.
-   `runkit7_constant_add` works. Other constant manipulation functions don't work for constants accessed within the same file due to the compiler inlining their values for performance.
-   Runkit superglobals.

#### Unsupported APIs for PHP7:
(These functions will be missing. Some of these should be possible to implement.)

-   `runkit_class_adopt` and `runkit_class_emancipate`
    Disabled because of [bugs related to properties](./PROPERTY_MANIPULATION.md).
-   `runkit7_import`
	This had known crashes in php 7.3+, and was removed in runkit7 4.0 because they weren't straightforward to fix.
	Use runkit7 3.x if you need to continue using this functionality.
-   `runkit_lint*`
    Might be possible if this is rewritten to use actual threading: See [issue #114](https://github.com/runkit7/runkit7/issues/114)
-   `runkit7_constant_*` : `runkit7_constant_add` works reliably, other methods don't.
    This works better when the constants are declared in a file separate from the file accessing that constant.
-   `runkit_default_property_*`
    Disabled because of [bugs related to properties](./PROPERTY_MANIPULATION.md)
    See [issue #30](https://github.com/runkit7/runkit7/issues/30) (implement function to modify only) and [issue #113](https://github.com/runkit7/runkit7/issues/113) (Manipulate static properties)

    `runkit_default_property_add` has been removed in php7 - it requires `realloc`ing a different zval to add a property to the property table
    That would break a lot of things (PHP internals, other PHP modules, etc)
-   `runkit_return_value_used`: Removed, was not working and unrelated to other features.
    `vld` seems to have a working implementation in the opcode analyzer, not familiar with how it works.

#### Reasons for disabling property manipulation

See [PROPERTY\_MANIPULATION.md](./PROPERTY_MANIPULATION.md)

### FURTHER WORK

See https://github.com/runkit7/runkit7/issues

Tasks for the near future:

-   Replace property manipulation with `runkit_default_property_modify` (https://github.com/runkit7/runkit7/issues/30)

### Contributing

See [CONTRIBUTING.md](./CONTRIBUTING.md) for guidelines on issues and pull requests, as well as links to resources that are useful for php7 module and runkit7 development.

UPSTREAM DOCUMENTATION
======================

**([runkit7](https://pecl.php.net/package/runkit7) is a fork of https://github.com/zenovich/runkit, implementing php7.2+ support)**

Features
========

Runkit has two groups of features outlined below (Sandboxing was removed in runkit7):

### CUSTOM SUPERGLOBALS
A new .ini entry `runkit.superglobal` is defined which may be specified as a simple variable, or list of simple variables to be registered as
[superglobals](https://secure.php.net/manual/en/language.variables.superglobals.php).  runkit.superglobal is defined as PHP_INI_SYSTEM and must be set in the system-wide php.ini.

Example:

php.ini:
```ini
runkit.superglobal=foo,bar
```

test.php:
```php
function testme() {
  echo "Foo is $foo\n";
  echo "Bar is $bar\n";
  echo "Baz is $baz\n";
}
$foo = 1;
$bar = 2;
$baz = 3;

testme();
```

Outputs:
```
Foo is 1
Bar is 2
Baz is
```


### USER DEFINED FUNCTION AND CLASS MANIPULATION

**NOTE: Only a subset of the APIs have been ported to PHP7. Some of these APIs have segmentation faults in corner cases** (when `runkit.internal_override=On`)

User defined functions and user defined methods may now be renamed, delete, and redefined using the API described at http://www.php.net/runkit

Examples for these functions may also be found in the tests folder.

#### `runkit_lint` alternatives

`runkit_lint` was  disabled with the rest of the sandbox code due to issues porting it to PHP 7 ([Issue #114](https://github.com/runkit7/runkit7/issues/114)).
As a replacement for `runkit_lint`/`runkit_lint_file` try any of the following:

- `php -l --no-php-ini $filename` will quickly check if a file is syntactically valid, but will not show you any php notices about deprecated code, etc.
- [`opcache_compile_file`](https://secure.php.net/manual/en/function.opcache-compile-file.php) may help, but will not show you any notices.
- [`token_get_all($code, TOKEN_PARSE)`](http://php.net/token_get_all) will detect invalid ASTs in php 7.0+
- Projects such as [PHP-Parser (Pure PHP)](https://github.com/nikic/PHP-Parser) and [php-ast (C module)](https://github.com/nikic/php-ast), which produce an Abstract Syntax Tree from php code.
  Alternately, `token_get_all()` will throw an error for syntax errors if the flag `TOKEN_PARSE` is passed in.
  (Unfortunately, it parses but does not detect erroneous code, e.g. duplicate classes/methods in the same file).

  ```php
  // Example replacement for runkit_lint.
  try {
      $ast = token_get_all('<?php function foo(){}', TOKEN_PARSE)
      return true;
  } catch (ParseError $e) {
      return false;
  }
  ```

  Alternately, you may wish to use a different approach and run a PHP static analyzer such as [Phan](https://github.com/phan/phan), [Psalm](https://github.com/vimeo/psalm), or [PHPStan](https://github.com/phpstan/phpstan)

Installation
============


### BUILDING AND INSTALLING RUNKIT7 IN UNIX

`pecl install runkit7` can be used to install [runkit7 releases published on PECL](https://pecl.php.net/package/runkit7).

Tarballs can be downloaded from [PECL](https://github.com/runkit7/runkit7/releases) or [GitHub](https://github.com/runkit7/runkit7/releases).

An example of how to build the latest master branch from source is below:

```bash
git clone https://github.com/runkit7/runkit7.git
cd runkit7
phpize
# The sandbox related code and flags have been removed, no need to disable them.
# (--enable-runkit7-modify (on by default) controls function, method, class, manipulation, and will control property manipulation)
# (--enable-runkit7-super (on by default) allows you to add custom superglobals)
# ./configure --help lists available configuration options.
./configure
make
make test
sudo make install
```

### BUILDING AND INSTALLING RUNKIT7 IN WINDOWS

#### Setting up php build environment

Read https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2 and https://wiki.php.net/internals/windows/stepbystepbuild_sdk_2#building_pecl_extensions first. This is just a special case of these instructions.

For PHP7.2+, you need to install "Visual Studio 2017 Community Edition" (or other 2017 edition).
For PHP8.0+, you need to install "Visual Studio 2019 Community Edition" (or other 2019 edition).
Make sure that C++ is installed with Visual Studio.
The command prompt to use is "VS2017 x86 Native Tools Command Prompt" on 32-bit, "VS2017 x64 Native Tools Command Prompt" on 64-bit.

- Note that different visual studio versions are needed for different PHP versions.
  For PHP 8.0+, use Visual Studio 2019 and vs16 instead.

For 64-bit installations of php7, use "x64" instead of "x86" for the below commands/folders.

After completing setup steps mentioned, including for `C:\php-sdk\phpdev\vc14`

extract download of php-7.4.11-src (or any version of php 7) to C:\php-sdk\phpdev\vc15\x86\php-7.4.11-src

#### Installing runkit7 on windows

There are currently no sources providing DLLs of this fork. Runkit7 and other extensions used must be built from source.

Create subdirectory C:\php-sdk\phpdev\vc14\x86\pecl, adjacent to php source directory)

extract download of runkit7 to C:\php-sdk\phpdev\vc14\x86\pecl\runkit7 (all of the c files and h files should be within runkit7)

Then, execute the following (Add `--enable-runkit7` to the configure flags you were already using)

```Batchfile
cd C:\php-sdk
C:\php-sdk\bin\phpsdk_setvars.bat
cd phpdev\vc15\x86\php-7.4.11\src
buildconf
configure --enable-runkit7
nmake
```

Then, optionally test it (Most of the tests should pass, be skipped, or be known failures.):

```Batchfile
nmake test TESTS="C:\php-sdk\vc14\x86\pecl\runkit7\tests
```
