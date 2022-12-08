<?php

require_once "framework/Model.php";

class User extends Model {

public function __construct(public string $email, public string $hashed_password, public string $role, public string $iban) {}

public static function validate_login(string $email, string $password) : array
{
	$errors = [];
	$user = User::get_user_by_email($email);
	if ($user) {
		if (!self::check_password($password, $user -> hashed_password)) {
			$errors[] = "Wrong password. Please try again.";
		}
	} else {
		$errors[] = "Can't find a user with the email '$email'. Please sign up.";
	}
	return $errors;
}

private static function check_password(string $clear_password, string $hash) : bool {
	return $hash === Tools::my_hash($clear_password);
}

public static function get_user_by_email(string $email) : User | false
{
	$query = self::execute("SELECT * FROM users WHERE mail = :email", ["email" => $email]);
	$data = $query -> fetch();
	if ($query -> rowCount() == 0) {
		return false;
	} else {
		return new User($data['email'], $data['hashed_password'], $data['full_name'], $data['role'], $data['iban']);
	}
}

}