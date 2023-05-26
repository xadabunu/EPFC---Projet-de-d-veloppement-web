<?php
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/User.php';
require_once "controller/MyController.php";
require_once "framework/Tools.php";

class ControllerMain extends MyController
{

    public function index(): void
    {
        if ($this->user_logged()) {
            $this->redirect("user", "my_tricounts");
        } else {
            $this->redirect("main", "login");
        }
    }

// --------------------------- Fonction Login && Signup -----------------------------------    


    public function login(): void
    {
        $email = "";
        $password = "";
        $errors = [];

        if ($this->get_user_or_false())
            $this->redirect();

        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $errors = User::validate_login($email, $password);
            if (count($errors) == 0) {
                $this->log_user(User::get_user_by_email($email), $controller = "user", "my_tricounts");
            }
        }
        (new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors]);
    }

    public function signup(): void
    {
        $email = '';
        $role = 'user';
        $full_name = '';
        $iban = '';
        $password = '';
        $password_confirm = '';
        $errors = [];

        if ($this->get_user_or_false())
            $this->redirect();

        if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password_confirm'])
            && isset($_POST['full_name']) && isset($_POST['iban'])) {

            $email = $_POST['email'];
            $full_name = $_POST['full_name'];
            $iban = $_POST['iban'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $user = new User($email, Tools::my_hash($password), $full_name, $role, $iban);
            $errors = User::validate_email_unicity($email);
            $errors = array_merge($errors, $user->validate());
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            if (count($errors) == 0) {
                $user->persist();
                $this->log_user($user);
            }
        }
        (new View("signup"))->show([
            "email" => $email, "password" => $password, "password_confirm" => $password_confirm,
            "full_name" => $full_name, "iban" => $iban, "errors" => $errors
        ]);
    }

    public function email_available_service(): void
    {
        $res = 'true';
        if ($this->get_user_or_false() && isset($_POST["email"]) && $_POST["email"] !== "" ) {
            $user = User::get_user_by_email($_POST["email"]);
            if ($user) {
                $res = "false";
                if (isset($_POST['user_email']) && $_POST['user_email'] === $_POST['email']) {
                    $res = 'true';
                }
            }
        }
        else {
            Tools::abort("Invalid or missing argument");
        }
        echo $res;
    }
}
