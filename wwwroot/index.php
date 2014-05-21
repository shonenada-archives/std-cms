<?php

define ("DOCROOT", realpath(__DIR__ . "/../") . DIRECTORY_SEPARATOR);
define ("PKGROOT", realpath(DOCROOT . "vendor/") . DIRECTORY_SEPARATOR);
define ("STDROOT", realpath(DOCROOT . "stdcms/") . DIRECTORY_SEPARATOR);
define ("WEBROOT", realpath(DOCROOT . "wwwroot/") . DIRECTORY_SEPARATOR);

if (!defined('START_TIME'))
    define('START_TIME', microtime(TRUE));

require(PKGROOT . "autoload.php");
require_once(STDROOT . "app.php");

$configs = array(
    WEBROOT . 'development.conf.php',
);

$app = createApp($configs);

$app->run();
