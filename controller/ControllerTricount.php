<?php

require_once "model/Tricount.php";
require_once "framework/Controller.php";
require_once "model/User.php";
require_once "model/Tricount.php";

class ControllerTricount extends Controller {

    public function index() : void
    {
        $user = $this -> get_user_or_redirect();
        $list = Tricount::get_tricounts_list($user->id);
        $subs_number = [];
        foreach ($list as $tricount) {
            $subs_number[$tricount -> id] = (Tricount::get_number_of_participants($tricount->id));
        }
        (new View("list_tricount")) -> show(["data" => $list, "subs_number" => $subs_number]);
   }

}