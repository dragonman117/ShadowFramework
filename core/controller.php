<?php
/*
 * Controller Class
 *
 *      This class will serve as a basis class for all module controllers. It will
 *      contain the basic view code and be used as a parent class for all model controllers.
 */
require_once("settings.php");
require_once("Twig/Autoloader.php");
Twig_Autoloader::register();

class Controller{
    var $dir = "";

    /*
     * Basic View Calls
     */
    function call_view($template, $vars){
        global $SETTINGS;
        $loader = new Twig_Loader_Filesystem($SETTINGS['template_base']);
        $twig = new Twig_Environment($loader);
        $file = $this->dir."/".$template;
        $vars = array_merge($this->gen_global(),$vars);
        echo $twig->render($file,$vars);
    }
    function render_to_string($template, $vars){
        global $SETTINGS;
        $loader = new Twig_Loader_Filesystem($SETTINGS['template_base']);
        $twig = new Twig_Environment($loader);
        $file = $this->dir."/".$template;
        $vars = array_merge($this->gen_global(),$vars);
        return $twig->render($file,$vars);
    }

    /*
     * Error View
     */
    function error404($error){
        global $SETTINGS;
        $loader = new Twig_Loader_Filesystem($SETTINGS['template_base']);
        $twig = new Twig_Environment($loader);
        $file = $SETTINGS['404'];
        $vars = $this->gen_global();
        $vars['error'] = $error;
        echo $twig->render($file,$vars);
    }

    /*
     * Helper Functions
     */
    //Generate the global variables for all templates
    function gen_global(){
        $all = array();
        global $SETTINGS;
        $all['base_url'] = $SETTINGS['base_url'];
        $all['full_url'] = $SETTINGS['full_url'];
        //Todo: All user based settings and any other type of settings here
        return $all;
    }

    /*
     * Authentication Checks
     */
    function auth($level){
        global $SETTINGS;
        $auth = new Authenticate();
        $tmp_check = $auth->check($_SESSION['user']['username'], $_SESSION['user']['token']);
        if($level <= $_SESSION['user']['auth_level'] && $tmp_check){
            return true;
        }else{
            $login = $SETTINGS['login_url'];
            $base_url = $SETTINGS['base_url'];
            header("Location: {$base_url}{$login}");
            die();
        }
    }
}
?>