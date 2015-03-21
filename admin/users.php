<?php
/*
 *  User Management
 *      This class will handle the administration of users
 */

require_once("core/controller.php");
require_once("core/model.php");
require_once("core/authenticate.php");

class Admin_users extends Controller{
    function __construct($res){
        $this->dir = "admin/users";
        //if there is no other view call the home view
        if(!array_key_exists(3,$res)){
            $res[3] = "home";
        }

        switch($res[3]){
            case "home":
                $this->home();
                break;
            case "edit":
                $this->ae($res);
                break;
            case "add":
                $this->ae($res);
                break;
            case "udPassword":
                $this->update($res);
                break;
            case "del":
                $this->delete($res);
                break;
        }
    }

    //Views
    function home(){
        $array = array();
        $model = new Model;
        $model->set_table("user");
        $array['users'] = $model->get_all();
        $this->call_view("home.html", $array);
    }

    function ae($init){
        $array = array();
        $model = new Model;
        $model->set_table("user");
        if(array_key_exists(4, $init)){
            $tmp = $model->get($init[4]);
            $array = $tmp;
            $array['type'] = "Edit";
            if($tmp){
                $array['data'] = $tmp;
            }else{
                $array['error'] = array('msg'=>"User not found");
            }
        }else{
            $array['type'] = "Add";
        }
        if($_POST){
            $auth = new Authenticate();
            if(array_key_exists('id', $_POST)){
                //We do update
                $errors = $auth->update_user($_POST);
                if($errors){
                    $array['error'] = $errors;
                    $array['error']['msg'] = "There was an error saving, please correct the errors below and try again.";
                }else{
                    $array['sucess'] = 2;
                }
                $array = array_merge($array, $_POST);
            }else{
                //This is a new user
                if(array_key_exists('auth_level', $_POST)){
                    $errors = $auth->register($_POST,$_POST['auth_level']);
                    if(is_int($errors)){
                        $array['id'] = $errors;
                        $errors = false;
                    }
                }else{
                    $errors['auth_level'] = 1;
                }
                if($errors){
                    //The save failed
                    $array['error'] = $errors;
                    $array['error']['msg'] = "There was an error saving, please correct the errors below and try agin.";
                    $array = array_merge($array, $_POST);
                }else{
                    $array['sucess'] = 1;
                }

            }
        }
        $this->call_view("ae.html",$array);
    }

    function update($init){
        $array = array();
        $array['id'] = $init[4];
        if($_POST){
            $auth = new Authenticate();
            $errors = $auth->change_password($init[4], $_POST);
            if($errors){
                $errors['msg'] = "There was an error changing the password, please try again.";
                $array['error'] = $errors;
            }else{
                $array['sucess'] = true;
            }
        }
        $this->call_view("change.html", $array);
    }

    function delete($init){
        $array = array();
        $array['id'] = $init[4];
        if($_POST){
            $model = new Model();
            $model->set_table('user');
            $model->delete($init[4]);
            $array['sucess'] = 1;
        }
        $this->call_view('del.html', $array);
    }
}
?>