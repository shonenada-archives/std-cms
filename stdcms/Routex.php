<?php

class Routex extends \Slim\Route {

    public function dispatch() {
        foreach ($this->middleware as $mw) {
            call_user_func_array($mw, array($this));
        }

        $return = call_user_func_array($this->getCallable(), array_values($this->getParams()));
        echo $return;

        return ($return === false || $return === "") ? false : true;
    }

}