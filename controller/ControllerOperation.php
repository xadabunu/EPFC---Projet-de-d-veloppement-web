<?php

require_once "controller/MyController.php";
require_once "model/User.php";
require_once "model/Template.php";

class ControllerOperation extends MyController
{
    public function index(): void
    {
    }

    public function details(): void
    {
        if (isset($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            $op = Operation::get_operation_by_id($_GET['param1']);
            $participants_list = $op->get_participants();
            $initiator = User::get_user_by_id($op->initiator);
            $prev = $op->get_previous();
            $next = $op->get_next();
            (new View("operation"))->show(["user" => $user,
                                            "operation" => $op,
                                            "participants_list" => $participants_list,
                                            "initiator" => $initiator,
                                            "next" => $next,
                                            "previous => $prev"]);
        }
        else {
            Tools::abort("Invalid or missing argument");
        }
    }

    public function add_operation() : void {
        $tricount = Tricount::get_tricount_by_id($_GET['param1']);
        $subscriptors = [];
        $operation = '';
        $subscriptors = $tricount->get_subscriptors_with_creator();
        $templates = Template::get_templates($tricount->id);
        $errors = [];
        if(isset($_POST['title'])) {
            $title = $_POST['title'];
            $amount = $_POST['amount'];
            $operation_date = $_POST['operation_date'];
            $created_at = Date("Y-m-d H:i:s");
            $initiator = $_POST['paid_by'];
            $operation = new Operation($title, $tricount->id, $amount, $operation_date, $initiator, $created_at);
            $errors = array_merge($errors, $operation->validate_operations());
            if(count($errors) == 0){
                $operation->persist_operation();
                $this->redirect('tricount', 'operations', $tricount->id); 
            }
        }
        (new View("add_operation"))->show(['tricount'=>$tricount, 'operation'=>$operation, 'subscriptors'=>$subscriptors,
                                            'templates'=>$templates, 'errors'=>$errors]);
    }
}