<?php

require_once "model/User.php";
require_once "model/Tricount.php";
require_once "controller/MyController.php";

class ControllerTricount extends MyController
{
    public function index(): void
    {
        $this->redirect("user", "my_trycounts");
    }

    public function operations(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1']))
        {
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            $list = $tricount->get_operations();
            (new View("operations"))->show(["list" => $list]);
        }
        else {
            Tools::abort("Invalid or missing argument.");
        }
    }

    public function add_tricount() : void{
        $title = '';
        $created_at = '';
        $creator = '';
        $description = '';
        $errors = [];
        if(isset($_POST['title'])){
            $title = $_POST['title'];
            $description = $_POST['description'];
            $creator = MyController::get_user_or_redirect();
            $created_at = date("Y-m-d H:i:s");
            $tricount = new Tricount($title, $created_at, $creator->id, $description);
            $errors = array_merge($errors, $tricount->validate());
            if (count($errors) == 0) {
                $tricount->persist_tricount();
                $this->redirect('tricount', 'operations', Tricount::lastTricountId());
            }    
        }
        (new View('add_tricount'))->show(["title"=>$title, "desciption"=>$description, "errors" =>$errors]);
    }
}
