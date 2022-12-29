<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";

class User extends Model
{

	public function __construct(public string $email, public string $hashed_password, public string $full_name , public string $role , public ?string $iban = null, public ?int $id = NULL  ) {}

	public static function validate_login(string $email, string $password): array
	{
		$errors = [];
		$user = User::get_user_by_email($email);
		if ($user) {
			if (!self::check_password($password, $user->hashed_password)) {
				$errors[] = "Wrong password. Please try again.";
			}
		} elseif (empty($email)) {
			$errors[] = "Please enter your email.";
		} else {
			$errors[] = "Can't find a user with the email '$email'. Please sign up.";
		}
		return $errors;
	}

	private static function check_password(string $clear_password, string $hash): bool
	{
		return $hash === Tools::my_hash($clear_password);
	}

	public static function get_user_by_email(string $email): User | false
	{
		$query = self::execute("SELECT * FROM users WHERE mail = :email", ["email" => $email]);
		$data = $query->fetch();
		if ($query->rowCount() == 0) {
			return false;
		} else {
			return new User($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["iban"], $data["id"]);
		}
	}

    public static function get_user_by_id(int $id) : User |false {
        $query = self::execute("SELECT * FROM users WHERE id =:id", ["id"=>$id]);
        $data = $query->fetch();
        if($query->rowcount() == 0) {
            return false;
        }
        else{
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["iban"], $data["id"]);
        }
    }

	public function get_user_tricounts() : array
	{
		$query = self::execute("SELECT t.* FROM tricounts t JOIN subscriptions s ON t.id = s.tricount WHERE s.user = :id UNION (SELECT * from tricounts where creator = :id) ", ["id" => $this->id]);
		$data = $query->fetchAll();

		$array = [];
		foreach ($data as $tricount) {
			$array[] = new Tricount($tricount['title'], $tricount['created_at'], $tricount['creator'], $tricount['description'], $tricount['id']);
		}
		return $array;
	}

    public static function validate_unicity(string $email) : array {
        $errors = [];
        $user = self::get_user_by_email($email);
        if ($user) {
            $errors[] = "This user already exists.";
        }
        return $errors;
    }

    public function validate() : array{
        $errors = [];
        if(!strlen($this->email) >0){
            $errors['required'] = "Email is required.";
        }
        if(!preg_match('/^[a-zA-Z0-9]{1,20}[@]{1}[a-zA-A0-9]{1,15}[.]{1}[a-z]{1,7}$/',$this->email)){
            $errors ['validity'] = "Not a valid email address";
        }
        if(!(strlen($this->full_name) >= 3)){
            $errors['lenght'] = "Pseudo length must be higher than 3.";
        }
        if(!preg_match("/^[a-zA-Z][a-zA-Z0]*$/", $this->full_name)){
            $errors ['name_contains'] = "Name must contains only letters";
        }
         if(!preg_match("/^BE[0-9]{2}\s[0-9]{4}\s[0-9]{4}\s[0-9]{4}$/", $this->iban)){
             $errors['iban'] = "IBAN must have an official IBAN format";
         }


        return $errors;
    }

    private static function validate_password(string $password) : array {
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors['password_lenght'] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors['password_format'] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    public static function validate_passwords(string $password, string $password_confirm) : array {
        $errors = self::validate_password($password);
        if ($password != $password_confirm) {
            $errors['password_confirm'] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    public function persist() : User {
        if(self::get_user_by_email($this->email)){
            self::execute("UPDATE users SET hashed_password=:password, full_name=:full_name, role=:role, iban=:iban WHERE mail=:email",
                            ["hashed_password"=>$this->hashed_password, "full_name"=>$this->full_name, "role"=>$this->role, "iban"=>$this->iban]);
        }
        else{
            self::execute("INSERT INTO users(mail, hashed_password, full_name, role, iban) VALUES(:email, :password, :full_name, :role, :iban)",
                            ["email" =>$this->email, "password"=>$this->hashed_password, "full_name"=>$this->full_name,"role"=>$this->role, "iban"=>$this->iban]);
        }
        $this->id = Model::lastInsertId();
        return $this;
    }
}
