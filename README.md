EventyClassic
=============

[![Latest Stable Version][icon-stable-version]][link-packagist]
[![Latest Untable Version][icon-unstable-version]][link-packagist]
[![Total Downloads][icon-downloads]][link-packagist]
[![License][icon-license]][link-license]
[![PHP][icon-php]][link-php]

[![Linux Build Status][icon-travis]][link-travis]
[![Windows Build Status][icon-appveyor]][link-appveyor]
[![Code Coverage][icon-coverage]][link-coverage]
[![Code Quality][icon-quality]][link-quality]

WordPress style actions and filters in classic (pure) PHP.

## About

Actions are pieces of code you want to execute at certain points in your code. Actions never return anything but merely serve as the option to hook in to your existing code without having to mess things up.

Filters are made to modify entities. They always return some kind of value. By default they return their first parameter and you should too. 

[Read more about filters](http://www.wpbeginner.com/glossary/filter/)

[Read more about actions](http://www.wpbeginner.com/glossary/action/)

This project is a fork from [Eventy for Laravel](https://github.com/tormjens/eventy/). The difference is that this project supports PHP 5.4 or later and it doesn't have any dependencies. Also, there are some differences in syntax.

## Use Cases

EventyClassic is best used as a way to allow extensibility to your code. Whether you're creating a package or an application, Eventy can bring the extensibility you need.

For example, EventyClassic can lay down the foundation for a plugin based system. You offer an "action" that allows plugins to register themselves. You might offer a "filter" so plugins can change the contents of an array in the core. You could even offer an "action" so plugins can modify the menu of your application.

## Installation

### Requirements

EventyClassic requires *PHP 5.4.0* or higher.

### Using Composer

The reccomended way to install EventyClassic is with [Composer][https://getcomposer.org/], dependency manager for PHP.

```bash
composer require filips123/eventy
```

You would only need to include autoloader and namespace in your script.

```php
<?php

use EventyClassic\Events as Eventy;

require 'vendor/autoload.php';

$eventy = new Eventy;
```

Ideally, class instance should be placed to dependency injection service.

### Manually Installation

Alternatively, you could download files from GitHub and then manually include them in your script.

You whould need to include all files and namespace in your script.

```php
<?php

use EventyClassic\Events as Eventy;

require 'src/Events.php';
require 'src/Event.php';
require 'src/Action.php';
require 'src/Filter.php';

$eventy = new Eventy;
```

Ideally, class instance should be placed to dependency injection service.

## Usage

### Actions

To listen to your hooks, you attach listeners.

For example if you wanted to hook in to the above hook, you could do:

```php
$eventy->addAction('my.hook', function($what) {
    echo 'You are '. $what;
}, 20, 1);
```

The first parameter is the name of the hook. The second would be a callback. This could be a Closure, an array callback (`[$object, 'method']`) or a globally registered function `function_name`. The third argument is the priority of the hook. The lower the number, the earlier the execution. The fourth parameter specifies the number of arguments your listener accepts.

You can then run actions:

```php
$eventy->runAction('my.hook', 'awesome');
```

Again the first argument must be the name of the hook. All subsequent parameters are sent to the action as parameters. These can be anything you'd like. For example you might want to tell the listeners that this is attached to a certain model. Then you would pass this as one of the arguments.

### Filters

Filters work in much the same way as actions and have the exact same build-up as actions. The most significant difference is that filters always return their value.

This is how you add a listener to this filter:

```php
$eventy->addFilter('my.hook', function($what) {
    $what = 'not '. $what;
    return $what;
}, 20, 1);
```

The filter would now return `'not awesome'`. Neat!

You could use this in conjunction with the previous hook:

```php
$eventy->addAction('my.hook', function($what) {
    $what = $eventy->runFilter('my.hook', 'awesome');
    echo 'You are '. $what;
});
```

You can then run filters:

```php
$value = $eventy->runFilter('my.hook', 'awesome');
```

If no listeners are attached to this hook, the filter would simply return `'awesome'`.

[icon-stable-version]: https://img.shields.io/packagist/v/filips123/eventy.svg?style=flat-square&label=Latest+Stable+Version
[icon-unstable-version]: https://img.shields.io/packagist/vpre/filips123/eventy.svg?style=flat-square&label=Latest+Unstable+Version
[icon-downloads]: https://img.shields.io/packagist/dt/filips123/eventy.svg?style=flat-square&label=Downloads
[icon-license]: https://img.shields.io/packagist/l/filips123/eventy.svg?style=flat-square&label=License
[icon-php]: https://img.shields.io/packagist/php-v/filips123/eventy.svg?style=flat-square&label=PHP
[icon-travis]: https://img.shields.io/travis/com/filips123/eventy.svg?style=flat-square&label=Linux+Build+Status
[icon-appveyor]: https://img.shields.io/appveyor/ci/filips123/eventy.svg?style=flat-square&label=Windows+Build+Status
[icon-coverage]: https://img.shields.io/scrutinizer/coverage/g/filips123/eventy.svg?style=flat-square&label=Code+Coverage
[icon-quality]: https://img.shields.io/scrutinizer/g/filips123/eventy.svg?style=flat-square&label=Code+Quality

[link-packagist]: https://packagist.org/packages/filips123/eventy/
[link-license]: https://choosealicense.com/licenses/mit/
[link-php]: https://php.net/
[link-travis]: https://travis-ci.com/filips123/eventy/
[link-appveyor]: https://ci.appveyor.com/project/filips123/eventy/
[link-coverage]: https://scrutinizer-ci.com/g/filips123/eventy/code-structure/
[link-quality]: https://scrutinizer-ci.com/g/filips123/eventy/
