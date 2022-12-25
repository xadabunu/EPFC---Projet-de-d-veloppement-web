<?php

require_once "controller/MyController.php";
require_once "model/User.php";
require_once "model/Template.php";

class ControllerOperation extends MyController
{
    public function index(): void {}

    public function details(): void
    {
        if (isset($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            $op = Operation::get_operation_by_id($_GET['param1']);
            $list = $op->get_participants();
            $amounts = [];
            foreach ($list as $participant) {
                $amounts[$participant->id] = $op->get_personnal_amount($participant->id);
            }
            $prev = $op->get_previous();
            $next = $op->get_next();
            (new View("operation"))->show(["user" => $user,
                                            "operation" => $op,
                                            "list" => $list,
                                            "next" => $next,
                                            "previous" => $prev,
                                            "amounts" => $amounts]);
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
        if(isset($_POST['title']) && isset($_POST['amount']) && isset($_POST['operation_date']) && isset($_POST['paid_by'])) {
            $title = $_POST['title'];
            $amount = $_POST['amount'];
            $operation_date = $_POST['operation_date'];
            $created_at = Date("Y-m-d H:i:s");
            $initiator = $_POST['paid_by'];
            if (is_numeric($amount) && is_numeric($initiator)) {
                $operation = new Operation($title, $tricount, $amount, $operation_date, User::get_user_by_id($initiator), $created_at);
                $errors = array_merge($errors, $operation->validate_operations());
                if (count($errors) == 0) {
                    $operation->persist_operation();
                    $this->redirect('tricount', 'operations', $tricount->id);
                }
            }
        }
        (new View("add_operation"))->show(['tricount'=>$tricount, 'operation'=>$operation, 'subscriptors'=>$subscriptors,
                                            'templates'=>$templates, 'errors'=>$errors]);
    }
}