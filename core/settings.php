<?php
/*
 * Settings.php
 *
 *      This file is used for all global settings, individual module globals
 *      need to be added to the Settings array via individual module array merge
 */

$SETTINGS = array(
    /*
     * Database Settings
     */
    'database'=>'',
    'host'=>'',
    'username'=>'',
    'password'=>'',


    /*
     * Url Settings
     * base url always needs to end with a "/"
     */
    //base is used if the install is located in a subdirectory and not the main html root
    'base_url'=>'/',
    //Full url is for the complete url of the install
    'full_url'=>'',

    /*
     * Login Settings
     */
    //Login url is the path from base-url to login page for redirects...
    'login_url'=> "admin/login",

    /*
     * E-Mail settings
     */
    'smtp'=>'',
    'eusername'=>'',
    'epassword'=>'',

    /*
     * Error Pages
     * All error pages should reside in the main template directory
     */
    '404' => "404.html",

    /*
     * Template Dir's
     * List the root template directory that all other templates is stored in
     */
    'template_base'=>"template",

    /*
     * Items per page
     * This is the pagination value for how many items to show per page on a pagination page
     */
    'ipp' => 10,
);

/*
 * Localhost Test settings
 *      This can be used to override settings that need to be changed for a local test install
 */
if(strcmp($_SERVER['HTTP_HOST'], 'localhost') == 0){
    /*
     * Localhost Database Settings
     */
    $SETTINGS['database'] = 'ShadowCMS';
    $SETTINGS['host'] = 'localhost';
    $SETTINGS['username'] = 'root';
    $SETTINGS['password'] = '';

    /*
     * Localhost Url Settings
     * base url always needs to end with a "/"
     */
    $SETTINGS['base_url'] = '/ShadowCMS/';
    $SETTINGS['full_url'] = 'http://localhost/ShadowCMS/';

}
?>