<?php

require_once "framework/Controller.php";
require_once "model/Tricount.php";
require_once "model/User.php";
require_once "controller/MyController.php";

class ControllerSettings extends MyController
{

    public function index(): void
    {
        $this->redirect("settings", "my_settings");
    }

// --------------------------- Vue Settings ------------------------------------ 

    public function my_settings(): void
    {
        $user = $this->get_user_or_redirect();
        (new View("settings"))->show(["user" => $user]);
    }

// --------------------------- Fonctions ------------------------------------ 


    public function edit_profile(): void
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $tmpUser = "";

        if (isset($_POST['email']) && isset($_POST['full_name']) && isset($_POST['iban'])) {
            $tmpUser = new User($_POST['email'], $user->hashed_password, $_POST['full_name'], $user->role, $_POST['iban']);
            $errors = array_merge($errors, $tmpUser->validate());
            if($tmpUser->email != $user->email){
                $errors = array_merge($errors, User::validate_email_unicity($tmpUser->email));
            }
            
            if (count($errors) == 0) {
                $user->email = $_POST['email'];
                $user->full_name = $_POST['full_name'];
                $user->iban = $_POST['iban'];
                $user->persist_update();
                $this->redirect('settings', 'my_settings');
            }
        }
        (new View("edit_profile"))->show(["user" => $user, 'errors' => $errors, "tmpUser" => $tmpUser]);
    }



    public function change_password(): void
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $current_password = '';
        $password = '';
        $password_confirm = '';

        if (isset($_POST['password']) && isset($_POST['password_confirm']) && isset($_POST['current_password'])) {
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $current_password = $_POST['current_password'];

            $errors = self::validate_current_password($current_password, $user->hashed_password);
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            $errors = array_merge($errors, self::validate_new_password($password, $user));

            if (count($errors) == 0) {
                $user->hashed_password = Tools::my_hash($password);
                $user->persist_update();
                $this->redirect('settings', 'my_settings');
            }
        }
        (new View("change_password"))->show(["user" => $user, 'errors' => $errors, 'password_confirm' => $password_confirm, 'password' => $password, 'current_password' => $current_password]);
    }

    private function validate_current_password(String $current_password, String $hashed_password): array
    {
        $errors = [];
        if (!User::check_password($current_password, $hashed_password)) {
            $errors['current_wrong_password'] = 'Wrong current password';
        }
        return $errors;
    }

    private function validate_new_password(String $new_password, User $user): array
    {
        $errors = [];
        if (Tools::my_hash($new_password) == $user->hashed_password) {
            $errors['password_validity'] = "The new password must be different from the old.";
        }
        return $errors;
    }

    public function email_available_service() : void {
        $res = 'true';
        if (isset($_GET["param1"]) && $_GET["param1"] !== "" ) {
            $user = User::get_user_by_email($_GET["param1"]);
            if ($user) {
                $res = "false";
            }
        }
        echo $res;
    }

}
