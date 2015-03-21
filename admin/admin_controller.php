<?php
/*
 * Admin Controller
 *
 *      This class is used to Direct the admin section of the site and take care of some basic views
 */

require_once("core/controller.php");
require_once("core/authenticate.php");
require_once("core/settings.php");

class Admin_controller extends Controller{

    function __construct($res){
        $this->dir = "admin";

        //if there is no other view call the home view
        if(!array_key_exists(2,$res)){
            $res[2] = "home";
        }
        //Special bypass authentiation is login
        if($res[2] == 'login'){
            $this->login();
            return true;
        }

        //Must be author or higher to view admin section (login page bypasses all restrictions);
        $this->auth(500);

        //Switch statement to correct view
        switch($res[2]){
            case "home":
                $this->home();
                break;
            case "users":
                $this->users($res);
                break;
            case "logout":
                $this->logout();
                break;
            default:
                $this->error404("Page Not found");
        }
    }

    /*
     *Views
     *      This is where the views will all be shown.
     */
    function home(){
        $vars = array();
        $this->call_view("home.html", $vars);
    }

    function login(){
        $array = array();
        if($_POST){
            $auth = new Authenticate();
            $test = $auth->login($_POST['username'], $_POST['password']);
            if($test){
                //Good login worked... otherwise you would need to fail redirect to dashboard
                global $SETTINGS;
                $login = $SETTINGS['login_url'];
                $base_url = $SETTINGS['base_url'];
                header("Location: {$base_url}admin/");
                die();
            }
            $array['error'] = true;
        }
        $this->call_view("login.html", $array);
    }

    function logout(){
        $auth = new Authenticate();
        //downgrade to guest
        $auth->guest();
        //Return to login page
        global $SETTINGS;
        $login = $SETTINGS['login_url'];
        $base_url = $SETTINGS['base_url'];
        header("Location: {$base_url}{$login}");
        die();
    }

    //Shadow Calls (calls to separate admin classes).
    function users($res){
        require_once("users.php");
        $test = new Admin_users($res);
    }

}
?>