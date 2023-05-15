<?php

require_once "framework/Controller.php";
require_once "model/Tricount.php";
require_once "model/User.php";
require_once "controller/MyController.php";

class ControllerUser extends MyController
{

    public function index(): void
    {
        $this->my_tricounts();
    }





// --------------------------- Liste des tricounts ------------------------------------ 


    public function my_tricounts(): void
    {
        $user = $this->get_user_or_redirect();
        $array = $user->get_user_tricounts();
        $subs_nb = [];
        foreach ($array as $tricount) {
            $nb = ($tricount->get_number_of_participants()) - 1;
            switch ($nb) {
                case 0:
                    $subs_nb[$tricount->id] = "You're alone";
                    break;
                case 1:
                    $subs_nb[$tricount->id] = "With 1 friend";
                    break;
                default:
                    $subs_nb[$tricount->id] = "With $nb friends";
                    break;
            }
            if (!$tricount->description) {
                $tricount->description = "No description";
            }
        }
        (new View("list_tricount"))->show(["data" => $array, "subs_number" => $subs_nb]);
    }
}
