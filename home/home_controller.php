<?php
/*
 * Home Controller
 *
 *      This class is a demonstration of a front end class used for user views and non authenticated access...
 */

require_once("core/controller.php");

class Home_controller extends Controller{
    function __construct($res){
        $this->dir = "home";

        //if there is no other view call the home view
        if(!array_key_exists(2,$res)){
            $res[1] = "home";
        }

        switch($res[1]){
            case "home":
                $this->home();
                break;
            default:
                $this->home();
                break;
        }
    }

    //Views
    function home(){
        $array = array();
        $this->call_view("home.html",$array);
    }
}

?>