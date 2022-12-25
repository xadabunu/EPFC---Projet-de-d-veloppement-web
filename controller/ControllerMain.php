<?php
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once "model/User.php";
require_once "controller/MyController.php";

class ControllerMain extends MyController
{

	public function index(): void
	{
		if ($this->user_logged()) {
			$this->redirect("user", "index");
		} else {
			$this->redirect("main", "login");
		}
	}

	public function login(): void
	{
		$email = "";
		$password = "";
		$errors = [];

		if (isset($_POST['email']) && isset($_POST['password'])) {
			$email = trim($_POST['email']);
			$password = trim($_POST['password']);

			$errors = User::validate_login($email, $password);
			if (count($errors) == 0) {
				$this->log_user(User::get_user_by_email($email), $controller = "user");
			}
		}
		(new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors]);
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
            && isset($_POST['full_name']) && isset($_POST['iban'])) {

            $email = trim($_POST['email']);
            $full_name = trim($_POST['full_name']);
            $iban = ($_POST['iban']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $user = new User($email, Tools::my_hash($password), $full_name, $role, $iban);
            $errors = User::validate_unicity($email);
            $errors = array_merge($errors, $user->validate());
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            if(count($errors) == 0){
                $user->persist();
                $this->log_user($user);
                // $this->redirect('main', 'signup_prg', $user->email, $password_confirm);
            }
        }
        // else{
        //     (new View("signup"))->show();
        // }
        (new View("signup"))->show(["email"=>$email, "password"=>$password, "password_confirm"=>$password_confirm,
                                    "full_name"=>$full_name, "iban"=>$iban, "errors"=>$errors]);
    }

    // public function signup_prg(){
    //     if(!empty($_GET['param1'])) {
    //         $email = $_GET['param1'];
    //         $user = User::get_user_by_email($email);
    //         $password_confirm = $_GET['param2'];
    //     }
    //     (new View("signup"))->show(["email"=>$email, "password"=>$user->password, "password_confirm"=>$password_confirm,
    //                                  "full_name"=>$user->full_name, "iban"=>$user->iban]);

    // }
}



