yii1-bugsnag
===================

Yii1 bugsnag error handler

Installation
------------
```code
composer require "demi/yii1-bugsnag" "~1.0"
```

Configuration
-------------
/protected/config/main.php:
```php
'components' => [
    'bugsnag' => [
        'class' => '\demi\bugsnag\yii1\BugsnagComponent',
        'bugsnagApiKey' => '<YOU API KEY>',
        'notifyReleaseStages' => ['production', 'development'],
    ],
],
```
/protected/config/console.php:
```php
$mainConfig = require(dirname(__FILE__) . '/main.php');
return [
    'components' => [
        'bugsnag' => $mainConfig['components']['bugsnag'],
    ],
],
```

Examples
--------
