<?php
/*
 * Authenticate class
 *
 *      This class is used to generate and manage all user information
 */

require_once("model.php");

class Authenticate{
    //User Table
    var $table = "user";

    //This is the user array that will be stored as default. If there are any unique values you need initialized do so here
    public $userData = array(
        'username' =>'Guest',
        'name' => 'Guest',
        'email' =>'',
        'auth_level' => 0
    );

    /*
     * Main Functions
     *      These functions Provide the general access to check the db
     */

    //This function will replace the userdata value in $_SESSION with our default guest array
    function guest(){
        $_SESSION['user'] = $this->userData;
    }

    //Register a user (checks post data for user info to register... so must be called after a registration form is sent)
    //      $user_level is a intiger from 0 - 1000 (1000 being a admin level), 0 being guest
    //      returns false if there is an error
    function register($raw, $user_level = 100){
        if($raw){
            $errors = $this->validate($raw);
            //If there are no errors add user to db
            if(!$errors){
                $data = array();
                $data['reg_date'] = time();
                $data['password'] = $this->hash_password($data['reg_date'], $raw['password']);
                $data['username'] = $raw['username'];
                $data['name'] = $raw['name'];
                $data['email'] = $raw['email'];
                $data['auth_level'] = $raw['auth_level'];
                $model = new Model();
                $model->set_table($this->table);
                $model->ae($data);
                return $model->last_link();
            }else{
                return $errors;
            }
            return false;
        }
        return false;
    }

    function change_password($id, $raw){
        $errors = array(); // 1 is not submitted, 2 not unique, 3 not matching, 4 not long enough
        if(array_key_exists('password', $raw) && array_key_exists('password2', $raw)){
            if($raw['password'] != $raw['password2']){
                $errors['password'] = 3;
            }
            if(strlen($raw['password']) < 8){
                $errors['password'] = 4;
            }
        } else {
            $errors['password'] = 1;
        }
        if(sizeof($errors)){
            return $errors;
        }else{
            $model = new Model();
            $model->set_table($this->table);
            $tmp = $model->get($id);
            if($tmp){
                $tmp['password'] = $this->hash_password($tmp['reg_date'], $raw['password']);
                $model->ae($tmp);
                return false;
            }
            else{
                $errors['password'] = 1;
            }
            return $errors;
        }
    }

    //This funtion checks for errors and returns errors if true, or updates if not returning false
    function update_user($raw){
        $model = new Model();
        $model->set_table($this->table);
        $tmp = $model->get($raw['id']);
        //Check if the uniqe values have changed
        $reValidate = array();
        $errors = array();
        if($tmp['username'] != $raw['username']){
            $reValidate['username'] = $raw['username'];
        }
        if($tmp['email'] != $raw['email']){
            $reValidate['email'] = $raw['email'];
        }
        if(count($reValidate) > 0){
            foreach($reValidate as $key=>$value){
                $test = $model->get($value, $key);
                if($test){
                    $errors[$key] = 2;
                }
            }
            if(count($errors) > 0 ){
                return $errors;
            }
        }
        //Remove the password in order to ensuer proper save (use password rest to updated passwords).
        if(array_key_exists('password', $raw)){
            unset($raw['password'], $raw['password2']);
        }
        $model->ae($raw);
        return false;
    }

    function login($username, $password){
        $model = new Model();
        $model->set_table($this->table);
        $tmp = $model->get($username, 'username');
        if($tmp){
            $tmp_pass = $this->hash_password($tmp['reg_date'], $password);
            if($tmp_pass == $tmp['password']){
                $token = session_id();
                $tmp['token'] = $this->set_token($tmp['id'], $token, $tmp['password']);
                unset($tmp['password']);
                $_SESSION['user'] = $tmp;
                return true;
            }
        }
        return false;
    }

    function authenticate(){
        if(array_key_exists('user', $_SESSION)){
            //So initial login has already taken place... initiate check
            if(array_key_exists('token', $_SESSION['user'])){
                //Check the token and hopefully all is good
                $check = $this->check($_SESSION['user']['username'], $_SESSION['user']['token']);
                if(!$check){
                    session_unset(); //End Session and remove all the other uses info (for protection)
                    $this->guest();
                }
            }else{
                //No user so guest
                //no need to unset the session as the only possible user data stored in this case is guest
                $this->guest();
            }
        }else{
            //No intitial login, so user is guest
            $this->guest();
        }
    }

    //Check if the user's session is valid
    function check($username, $token){
        $model = new Model;
        $model->set_table($this->table);
        $tmp = $model->get($username, 'username');
        //Check the negitive statements.... other wise it should be fine.
        if(!$tmp){
            return fasle;
        }else if($tmp['token'] != $token){
            return false;
        }
        return true;
    }

    /*
     * Helper Functions
     *      These are functions which are meant to help manipulate and prep data for the DB
     */
    private function set_token($user_id, $session_id, $password){
        $model= new Model();
        $model->set_table($this->table);
        $date = date('Ymd');
        $raw_token = $session_id.$date.substr($password,7,20);
        $data = array();
        $data['id'] = $user_id;
        $data['token'] = hash('sha256', $raw_token);
        $model->ae($data);
        return $data['token'];
    }

    //This is the function that actually does the password hashing...
    private function hash_password($reg_date, $pass){
        $pre = $this->encode($reg_date);
        $pos = substr($reg_date, 5, 1);
        $post = $this->encode($reg_date * (substr($reg_date, $pos, 1)));
        //Inject password hash here
        if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH){
            $salt = '$2y$11$' . substr(md5($pre), 0, 22);
        }
        $password = crypt($pre.$pass.$post, $salt);
        return $password;
    }
    //Validate the passwords given match ... expect an array with keys password, password2, username, email
    private function validate($array){
        $errors = array(); // 1 is not submitted, 2 not unique, 3 not matching, 4 not long enough
        if(array_key_exists('password', $array) && array_key_exists('password2', $array)){
            if($array['password'] != $array['password2']){
                $errors['password'] = 3;
            }
            if(strlen($array['password']) < 8){
                $errors['password'] = 4;
            }
        } else {
            $errors['password'] = 1;
        }
        //add in all required unique values
        //should other values be required to be unique you can add them to the array and below to check
        $unique = array(
            'username'=>$array['username'],
            'email'=>$array['email'],
        );
        $model = new Model;
        $model->set_table($this->table);
        foreach($unique as $key=>$value){
            $test = $model->get($value, $key);
            if($test){
                $errors[$key] = 2;
            }
        }
        //Check required elements
        $req = array('username', 'name', 'email', 'auth_level');
        foreach($req as $val){
            if(!array_key_exists($val, $array)){
                $errors[$val] = 1;
            }elseif(strlen($array[$val])< 1){
                var_dump(strlen($array[$val]));
                $errors[$val] = 1;
            }
        }
        if(sizeof($errors)){
            return $errors;
        }else{
            return false;
        }
    }

    //borrowed from Uflex found at: https://github.com/ptejada/uFlex
    public $encoder = array(
        "a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
        "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
        0,2,3,4,5,6,7,8,9
    );
    private function encode($d){
        $k=$this->encoder;
        preg_match_all("/[1-9][0-9]|[0-9]/",$d,$a);
        $n="";
        $o=count($k);
        foreach($a[0]as$i){
            if($i<$o){
                $n.=$k[$i];
            }else{
                $n.="1".$k[$i-$o];
            }
        }
        return $n;
    }
    private function hextobin($hexstr){
        $n = strlen($hexstr);
        $sbin="";
        $i=0;
        while($i<$n){
            $a =substr($hexstr,$i,2);
            $c = pack("H*",$a);
            if ($i==0){$sbin=$c;}
            else {$sbin.=$c;}
            $i+=2;
        }
        return $sbin;
    }
}
?>