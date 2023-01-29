<?php 

require_once "framework/Controller.php";
require_once "model/Tricount.php";
require_once "model/User.php";
require_once "controller/MyController.php";


class ControllerSettings extends MyController{

    public function index(): void {
        $this->redirect("settings", "my_settings");
    }

// --------------------------- Vue Settings ------------------------------------ 

    public function my_settings(): void {
        $user = $this->get_user_or_redirect();
        
        
        (new View("settings"))->show(["user" => $user]);

    }

// --------------------------- Fonctions ------------------------------------ 


    public function edit_profile(): void {
        $user = $this->get_user_or_redirect();
        $errors = [];

        if (isset($_POST['email']) && isset($_POST['full_name']) && isset($_POST['iban'])) {

            }

        (new View("edit_profile"))->show(["user" => $user, 'errors'=>$errors]);
    }

    public function change_password(): void {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $password = '';
        $password_confirm = '';

        (new View("change_password"))->show(["user" => $user, 'errors'=>$errors, 'password_confirm'=>$password_confirm, 'password'=>$password]);
    }
    
}