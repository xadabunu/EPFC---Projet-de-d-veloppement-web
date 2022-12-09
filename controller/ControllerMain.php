<?php
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/User.php';

class ControllerMain extends Controller {

    public function index() : void
    {
	$this -> login();
    }

    public function login() : void {

	    $email = "";
	    $password = "";
	    $errors = [];

	    if (isset($_POST['email']) && isset($_POST['password']))
	    {
		    $email = trim($_POST['email']);
		    $password = trim($_POST['password']);

		    $errors = User::validate_login($email, $password);
		    if (empty($errors)) {
			    $this -> log_user(User::get_user_by_email($email));
		    }
	    }
	(new View("login")) -> show(["email" => $email, "password" => $password, "errors" => $errors]);
    }

    public function signup() : void {
        $email = '';
        $role='user';
        $full_name = '';
        $iban = '';
        $password = '';
        $password_confirm = '';
        $errors = [];

        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password_confirm'])
            && isset($_POST['fullName']) && isset($_POST['iban'])) {

            $email = trim($_POST['email']);
            $full_name = trim($_POST['fullName']);
            $iban = ($_POST['iban']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $user = new User($email, $full_name, Tools::my_hash($password),$role, $iban);
            $errors = User::validate_unicity($email);
            $errors = array_merge($errors, $user->validate());
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            if(count($errors) == 0){
                $user->persist();
                $this->log_user($user);
            }
        }
        (new View("signup"))->show(["email"=>$email, "password"=>$password, "password_confirm"=>$password_confirm,
                                    "full_name"=>$full_name, "iban"=>$iban, "errors"=>$errors]);
    }
}