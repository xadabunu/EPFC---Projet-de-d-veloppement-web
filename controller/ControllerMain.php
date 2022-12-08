<?php

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

}