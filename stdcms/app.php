<?php

function createApp ($configFiles=array()) {
    $boot = require_once(STDROOT . 'bootstrap.php');

    if(!is_array($configFiles))
        exit('Config files are not array.');

    $app = new Slimx();
    \GlobalEnv::set('app', $app);
    \Controller\Base::setApp($app);

    $config = require_once(STDROOT . 'config/common.php');
    $app->config($config);

    foreach($configFiles as $path)
        $app->config(require_once($path));

    \Extension\Auth::setup($app);
    \Extension\View::setup($app);
    \Extension\Middleware::setup($app);

    $translation = \Model\Lang::getByCode($app->config('translation.default.code'));
    \GlobalEnv::set('translation.default', $translation);

    $app->loadControllers($boot['controllers']);

    return $app;
}
