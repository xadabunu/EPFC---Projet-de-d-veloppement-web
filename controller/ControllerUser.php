<?php

require_once "framework/Controller.php";
require_once "model/Tricount.php";
require_once "model/User.php";

class ControllerUser extends Controller
{

    public function index(): void
    {
        $this->my_tricounts();
    }

    public function my_tricounts(): void
    {
        $user = $this->get_user_or_redirect();
        $array = $user->get_user_tricounts();
        $subs_nb = [];
        foreach ($array as $tricount) {
            $subs_nb[$tricount->id] = ($tricount->get_number_of_participants() - 1);
        }
        (new View("list_tricount"))->show(["data" => $array, "subs_number" => $subs_nb]);
    }
}
