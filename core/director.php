<?php
/*
 * Director Class
 *
 *      This class will be the entry class into the application. It will
 *      take the the raw url and break it down into the individual peaces
 *      and call the corresponding module's controller.
 */

require_once('settings.php');
require_once('urls.php');
require_once('authenticate.php');

class Director {
    var $raw = '';

    /*
     * Constructor Function
     * This will do final direction using helper functions to build everything.
     */
    function __construct(){
        $this->raw =  $_SERVER['REQUEST_URI'];
        global $URLS, $SETTINGS;

        //User Session Control starts
        session_start();
        $auth = new Authenticate();
        $auth->authenticate();

        foreach ($URLS as $value){
            if(preg_match('*'.$SETTINGS['base_url'].$value[1].'*', $this->raw, $result) == 1){
                $path= strtolower($value[0]);
                require_once($path.'/'.$path.'_controller.php');
                $class = $value[0].'_controller';
                $intance = new $class($result);
                return true;
            }
        }
    }

    //Helper Functions

}