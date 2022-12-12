<?php

require_once "model/User.php";

class ControllerMain extends Controller
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
				$this->log_user(User::get_user_by_email($email), $controller = "Tricount");
			}
		}
		(new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors]);
	}
}
