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
            (new View("tricount"))->show(["list" => $list, "tricount"=>$tricount]);
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
            $created_at = Date("Y-m-d H:i:s");
            $tricount = new Tricount($title, $created_at, $creator->id, $description);
            $errors = array_merge($errors, $tricount->validate());
            if (count($errors) == 0) {
                $tricount->persist_tricount();
                $this->redirect('tricount', 'operations', Tricount::lastTricountId());
            }    
        }
        (new View('add_tricount'))->show(["title"=>$title, "desciption"=>$description, "errors" =>$errors]);
    }

    public function edit_tricount() : void{
        $subscriptors = [];
        $creator = '';
        if (isset($_GET['param1'])){
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            $creator = User::get_user_by_id($tricount->creator);
            $subscriptors = $tricount->get_subscriptors();
            $errors = [];
            $cbo_users = $tricount->get_cbo_users();
            if(isset($_POST['title']) || isset($_POST['description'])){
                $tricount->title = $_POST['title'];
                $tricount->description = $_POST['description'];
                $errors = array_merge($errors, $tricount->validate());
                if (count($errors) == 0) {
                    $tricount->persist_tricount();
                    $this->redirect('tricount', 'edit_tricount', $_GET['param1']);
                }
            }
            (new View("edit_tricount"))->show(['tricount'=>$tricount, 'subscriptors'=>$subscriptors,'creator'=>$creator,
                                             'errors'=>$errors, 'cbo_users'=>$cbo_users]);
        }
        else {
            Tools::abort("Invalid or missing argument.");
        }
        
    }

    public function add_subscriptors() : void {
        if(isset($_POST['subscriptor'])){
            $subscriptor = $_POST['subscriptor'];
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            $tricount->persist_subscriptor($subscriptor);
            $this->redirect('tricount', 'edit_tricount', $_GET['param1']);
        }         
    }

    public function delete_subscriptor() : void {
        if(isset($_POST['subscriptor_name'])){
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            $subscriptor = $_POST['subscriptor_name'];
            $tricount->delete_subscriptor($subscriptor);
            $this->redirect('tricount', 'edit_tricount', $_GET['param1']);
        }

    }

    public function delete_tricount() : void {
        $tricount = Tricount::get_tricount_by_id($_GET['param1']);
        $tricount->delete_tricount_cascade();
        $this->redirect('user', 'index');
    }
}
