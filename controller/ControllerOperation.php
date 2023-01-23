<?php

require_once "controller/MyController.php";
require_once "model/User.php";
require_once "model/Template.php";

class ControllerOperation extends MyController
{

// --------------------------- Index + Details Operations ------------------------------------ 


    public function index(): void {}

    public function details(): void
    {
        if (isset($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            $op = Operation::get_operation_by_id($_GET['param1']);
            $list = $op->get_participants();
            $amounts = [];
            foreach ($list as $participant) {
                $amounts[$participant->id] = $op->get_user_amount($participant->id);
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

// --------------------------- Add/edit Operations ------------------------------------ 


    public function add_operation() : void
    {
        $tricount = Tricount::get_tricount_by_id($_GET['param1']);
        $operation = '';
        $subscriptors = $tricount->get_subscriptors_with_creator();
        $templates = Template::get_templates($tricount->id);
        $errors = [];
        $title = '';
        $amount = '';
        $operation_date = '';
        $initiator = '';
        if(isset($_POST['title']) && isset($_POST['amount']) && isset($_POST['operation_date']) && isset($_POST['paid_by'])) {
            $title = Tools::sanitize($_POST['title']);
            $amount = Tools::sanitize(floatval($_POST['amount']));
            $operation_date = Tools::sanitize($_POST['operation_date']);
            $created_at = Date("Y-m-d H:i:s");
            $initiator = $_POST['paid_by'];
            $list = self::get_weight($_POST, $tricount);
            $errors = array_merge($errors, self::is_valid_fields($_POST));
            if(count($errors) == 0){
                $operation = new Operation($title, $tricount, $amount, $operation_date, User::get_user_by_id($initiator), $created_at);
                $errors = $operation->validate_operations();
                if (count($errors) == 0) {
                    $operation->persist_operation();
                    $operation->persist_repartition($operation, $list);
                    $this->redirect('tricount', 'operations', $tricount->id);
                }
            }
        }
        (new View("add_operation"))->show(['tricount'=>$tricount, 'operation'=>$operation, 'subscriptors'=>$subscriptors,
                                            'templates'=>$templates, 'errors'=>$errors, 'title'=>$title, 'amount'=>$amount,
                                            'operation_date'=>$operation_date, 'initiator'=>$initiator]);
    }

    private function is_valid_fields(array $array) : array
    {
        $errors = [];
        if(empty($array['title'])){
            $errors ['empty_title'] = "Title is required";
        }
        if(empty($array['amount'])) {
            $errors['empty_amount'] = "Amount is required";
        }
        if(empty($array['paid_by'])) {
            $errors['empty_initiator'] = "You must choose an initiator";
        }
        if(empty($array['operation_date'])){
            $errors['empty_date'] = "Date of your operation is required";
        }
        $cpt = 0;
        $list = [];
        foreach($array as $var) {
            if(is_numeric($var)){
                $cpt += 1;
                $list[] = $var;
            }
        }
        if($cpt == 0){
            $errors['whom'] = "You must choose at least one person";
        }
        foreach ($list as $var) {
            if ($var <= 0) {
                $errors['whom'] = "Weight must be strictly positive";
            }
        }

        return $errors;
    }

    public function edit_operation() : void
    {
        $subscriptors = [];
        $templates = '';
        $operation = '';
        $errors = [];
        $list = [];
        if(isset($_GET['param1'])){
            $operation = Operation::get_operation_by_id($_GET['param1']);
            $tricount = $operation->tricount;
            $subscriptors = $tricount->get_subscriptors_with_creator();
            $templates = Template::get_templates($tricount->id);
            $list = $operation->get_repartitions();
            if(isset($_POST['title']) && isset($_POST['amount']) && isset($_POST['operation_date'])) {
                $operation->title = Tools::sanitize($_POST['title']);
                $operation->amount = Tools::sanitize($_POST['amount']);
                $operation->initiator = User::get_user_by_id($_POST['paid_by']);
                $operation->operation_date = $_POST['operation_date'];
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST));
                $errors = array_merge($errors, $operation->validate_operations());
                if(count($errors) == 0){
                    $operation->persist_repartition($operation, $list);
                    $operation->persist_operation();
                    $this->redirect('tricount', 'operations', $tricount->id);
                }
            }
        }
        (new View('edit_operation'))->show(['operation'=>$operation, 'errors'=>$errors,
                                            'subscriptors'=>$subscriptors, 'templates'=>$templates, 'list'=>$list]);
    }
//---- Fonction private get sur le poids et les users selectionnés lors d'un add ou edit operation

    private function get_whom(array $array, Tricount $tricount) : array
    {   
        $list = $tricount->get_subscriptors_with_creator();
        $result = [];
        foreach($list as $sub) {
            if(array_key_exists($sub->id, $array)){
                $result[] = $sub;
            }
        }
        return $result;
    }

    private function get_weight(array $array, Tricount $tricount) : array
    {
        $list = self::get_whom($array, $tricount);
        $result = [];
        foreach($list as $sub) {
            $result[$sub->id] = $array['weight_'.$sub->id];
        }
        return $result;
    }


// --------------------------- Delete + ConfirmDelete operations ------------------------------------ 


    public function delete_operation() : void
    {
        $operation = Operation::get_operation_by_id($_GET['param1']);
        (new View('delete_operation'))->show(['operation'=>$operation]);
    }

    public function confirm_delete_operation() : void
    {
        $operation = Operation::get_operation_by_id($_GET['param1']);
        $operation->delete_operation_cascade();
        $this->redirect('tricount', 'operations', $operation->tricount->id);
    }



}