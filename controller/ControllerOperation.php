<?php

require_once "controller/MyController.php";
require_once "model/User.php";
require_once "controller/ControllerTemplates.php";
require_once "model/RepartitionTemplateItems.php";
require_once "model/RepartitionTemplates.php";
require_once "model/Repartitions.php";

class ControllerOperation extends MyController
{
// --------------------------- Index + Details Operations ------------------------------------ 

    public function index(): void
    {
        if ($this->user_logged()) {
            $this->redirect("user", "my_tricounts");
        } else {
            $this->redirect("main", "login");
        }
    }

    public function details(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            $op = Operation::get_operation_by_id($_GET['param1']);
            if (!$op->tricount->has_access($user))
                $this->redirect();
            $list = $op->get_participants();
            $amounts = [];
            foreach ($list as $participant) {
                $amounts[$participant->id] = $op->get_user_amount($participant->id);
            }
            (new View("operation"))->show([
                "user" => $user,
                "operation" => $op,
                "list" => $list,
                "next" => $op->get_next(),
                "previous" => $op->get_previous(),
                "amounts" => $amounts
            ]);
        } else {
            Tools::abort("Invalid or missing argument");
        }
    }


// --------------------------- Add/edit Operations ------------------------------------


    public function add_operation(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            if (!$tricount->has_access($user))
                $this->redirect();
            $operation = new Operation("", Tricount::get_tricount_by_id($_GET["param1"]), 0, date("Y-m-d"), $user, date("Y/m/d"));
            $repartition_template='';
            $errors = [];
            $list = [];
            $repartition_template_choosen = '';
            if (isset($_POST['title']) && isset($_POST['amount']) && isset($_POST['operation_date']) && isset($_POST['paid_by'])) {
                
                $operation = new Operation(
                    trim($_POST['title']),
                    Tricount::get_tricount_by_id($_GET['param1']),
                    floatval($_POST['amount']),
                    $_POST['operation_date'],
                    is_numeric($_POST['paid_by']) ? User::get_user_by_id(($_POST['paid_by'])) : null,
                    Date("Y-m-d H:i:s")
                );

                if (isset($_POST['template_title'])) {
                    $repartition_template = new RepartitionTemplates(trim($_POST["template_title"]), $tricount);
                }
                
                if ($_POST['amount'] <= 0) {
                    $errors ['amount'] = 'Amount must be strictly positive' ;
                }
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST, $tricount));

                if (isset($_POST['templates']) && is_numeric($_POST['templates'])) {
                    $repartition_template_choosen = RepartitionTemplates::get_repartition_template_by_id($_POST['templates']);                    
                }

                $errors = $operation->validate_operations();
                if (count($errors) == 0) {
                    if (isset($_POST["save_template_checkbox"])) {
                        
                        $errors = array_merge($errors, $repartition_template->validate_repartition_template());
                    }

                    if (count($errors) == 0) {
                        if (isset($_POST["save_template_checkbox"])) {
                            $repartition_template->add_repartition_template_from_operation($list, $repartition_template);
                        }
                        $operation->persist_operation();
                        $operation->persist_repartition($operation, $list);
                        $this->redirect('tricount', 'operations', $tricount->id);
                    }
                }
            }
            (new View("add_operation"))->show([
                'operation' => $operation,'errors' => $errors,'list'=>$list, 'templateChoosen' => $repartition_template_choosen, 'repartition_template' => $repartition_template]);
        } else
            Tools::abort("Invalid or missing argument.");
    }

    private function is_valid_fields(array $array, Tricount $tricount): array
    {
        $errors = [];
        if (empty($array['title'])) {
            $errors['empty_title'] = "Title is required";
        }
        if (empty($array['amount'])) {
            $errors['empty_amount'] = "Amount is required";
        }
        if (empty($array['paid_by'])) {
            $errors['empty_initiator'] = "You must choose an initiator";
        }
        if (empty($array['operation_date'])) {
            $errors['empty_date'] = "Date of your operation is required";
        }
       
        $listUser = self::get_whom($array, $tricount);

        if(count($listUser) == 0 ){
            $errors['whom'] = "You must choose at least one person";
        }

        $id = [];
        foreach($listUser as $user){
            $id[] = $user->id;
        }
        
        foreach($array as $key => $item){ 
            if(substr($key, 0, 6) == "weight"){
                if(in_array(substr($key, 7), $id)){
                    if(!is_numeric($item) || intval($item) < 1){
                        $errors['weight'] = "Weight must be a strictly positive numeric value";
                    }
                }
            }
        }

        if (isset($array["save_template_checkbox"]) && empty($array["template_title"])) {
            $errors['empty_template_title'] = "Template title is required";
        }

        return $errors;
    }

    public function edit_operation(): void
    {
        $operation = '';
        $errors = [];
        $list = [];
        $repartition_template_choosen = [];

        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $operation = Operation::get_operation_by_id($_GET['param1']);
            $user = $this->get_user_or_redirect();
            if (!$operation->tricount->has_access($user))
                $this->redirect();
            $tricount = $operation->tricount;

            if (isset($_POST['title']) && isset($_POST['amount']) && isset($_POST['operation_date'])) {
                $operation->title = ($_POST['title']);
                $operation->amount = floatval($_POST['amount']);
                $operation->initiator = User::get_user_by_id($_POST['paid_by']);
                $operation->operation_date = $_POST['operation_date'];
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST, $tricount));
                $errors = array_merge($errors, $operation->validate_operations());

                if (isset($_POST['templates']) && is_numeric($_POST['templates'])) {
                    $repartition_template_choosen = RepartitionTemplates::get_repartition_template_by_id($_POST['templates']);
                }

                if (isset($_POST["save_template_checkbox"])) {
                    $repartition_template = new RepartitionTemplates($_POST["template_title"], $tricount);
                    $errors = array_merge($errors, $repartition_template->validate_repartition_template());
                }

                if (count($errors) == 0) {
                    if (isset($_POST["save_template_checkbox"])) {
                        $repartition_template->add_repartition_template_from_operation($list, $repartition_template);
                    }
                    $operation->persist_repartition($operation, $list);
                    $operation->persist_operation();
                    $this->redirect('tricount', 'operations', $tricount->id);
                }
            }
        (new View('edit_operation'))->show([
            'operation' => $operation, 'errors' => $errors, 'list' => $list, 'templateChoosen' => $repartition_template_choosen]);
        }else{
            Tools::abort("Invalid or missing argument");
        }
    }


//---- Fonction private get sur le poids et les users selectionnés lors d'un add ou edit operation

    private function get_whom(array $array, Tricount $tricount): array
    {
        $list = $tricount->get_subscriptors_with_creator();
        $result = [];
        foreach ($list as $sub) {
            if (array_key_exists($sub->id, $array)) {
                $result[] = $sub;
            }
        }
        return $result;
    }

    private function get_weight(array $array, Tricount $tricount): array
    {
        $list = self::get_whom($array, $tricount);
        $result = [];
        foreach ($list as $sub) {
            $result[$sub->id] = $array['weight_' . $sub->id];
        }
        return $result;
    }


// --------------------------- Delete + ConfirmDelete operations ------------------------------------ 


    public function delete_operation(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])){
            $user = $this->get_user_or_redirect();
            $operation = Operation::get_operation_by_id($_GET['param1']);
            if (!$operation->tricount->has_access($user))
                $this->redirect();
            (new View('delete_operation'))->show(['operation' => $operation]);
        } else {
            Tools::abort('Invalid or missing argument.');
        }
    }    

    public function confirm_delete_operation(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])){
            $operation = Operation::get_operation_by_id($_GET['param1']);
            $operation->delete_operation_cascade();
            $this->redirect('tricount', 'operations', $operation->tricount->id);
        } else {
            Tools::abort('Invalid or missing argument.');
        }       

    }

    public function delete_operation_service()
    {
        $user = $this->get_user_or_false();
        if (isset($_GET["param1"])) {
            $operation = Operation::get_operation_by_id($_GET["param1"]);
        }
        if ($user && $operation && $operation->tricount->has_access($user)) {
            $operation->delete_operation_cascade();
            echo "true";
        }
        else
            echo "false";
    }

// --------------------------- Apply template for add/edit operation ------------------------------------ 

    public function apply_template_edit_operation(): void
    {
        $operation = '';
        $errors = [];
        $list = [];
        $title = '';
        $amount = '';
        $operation_date = '';
        $paid_by = '';
        $repartition_template_choosen = [];

        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            $operation = Operation::get_operation_by_id($_GET['param1']);
            if (!$operation->tricount->has_access($user))
                $this->redirect();

            if (isset($_POST['title'])) {
                $title = $_POST['title'];
            }
            if (isset($_POST['amount'])) {
                $amount = $_POST['amount'];
            }
            if (isset($_POST['operation_date'])) {
                $operation_date = $_POST['operation_date'];
            }
            if (isset($_POST['paid_by'])) {
                $paid_by = User::get_user_by_id($_POST['paid_by']);
            }
            if (isset($_POST['templates']) && is_numeric($_POST['templates']) ) {
                $repartition_template_choosen = RepartitionTemplates::get_repartition_template_by_id($_POST['templates']);
            } 

            (new View('edit_operation'))->show([
                'operation' => $operation, 'errors' => $errors, 'list' => $list,
                'titleValue' => $title, 'amountValue' => $amount, 'operation_dateValue' => $operation_date, 'paid_byValue' => $paid_by,
                'templateChoosen' => $repartition_template_choosen]);
        } else
            Tools::abort("Invalid or missing argument.");
    }

    public function apply_template_add_operation(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            if (!$tricount->has_access($user))
                $this->redirect();
            $operation = '';
            $errors = [];
            $list = [];
            $title = '';
            $amount = '';
            $operation_date = '';
            $initiator = '';
            $repartition_template_choosen = '';

            if (isset($_POST['title'])) {
                $title = $_POST['title'];
            }
            if (isset($_POST['amount'])) {
                $amount = $_POST['amount'];
            }
            if (isset($_POST['operation_date'])) {
                $operation_date = $_POST['operation_date'];
            }
            if (isset($_POST['paid_by']) && is_numeric($_POST['paid_by'])) {
                $initiator = User::get_user_by_id($_POST['paid_by']);
            }
            if (isset($_POST['templates']) && is_numeric($_POST['templates'])) {
                $repartition_template_choosen = RepartitionTemplates::get_repartition_template_by_id($_POST['templates']);
            }
            (new View("add_operation"))->show([
                'tricount' => $tricount, 'operation' => $operation,
                'errors' => $errors, 'title' => $title, 'amount' => $amount,
                'operation_date' => $operation_date, 'initiator' => $initiator, 'list'=>$list,
                'templateChoosen' => $repartition_template_choosen]);
        } else
            Tools::abort("Invalid or missing argument.");
    }

// --------------------------- Javascipt Apply template for add/edit operation ------------------------------------ 

    public function get_repartition_template_by_id_as_json():void
    {
        if ($this->get_user_or_false() && isset($_GET["param1"]) && is_numeric($_GET["param1"])) {
            echo RepartitionTemplates::get_repartition_template_by_id_as_json(intval($_GET["param1"]));
        } else 
            $this->redirect();
    }

    public function get_repartition_template_items_by_repartition_template_id_as_json(): void
    {
        if ($this->get_user_or_false() && isset($_GET["param1"]) && is_numeric($_GET["param1"])) {
            echo RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id_as_json(intval($_GET["param1"]));
        }
        else
            $this->redirect();
    }
    
    public function template_title_available(): void {
        $res = "true";
        if ($this->get_user_or_false() && isset($_POST["title"]) && $_POST["title"] !== "" && isset($_POST["tricount"]) && $_POST["tricount"] !== ""){
            $template_title = RepartitionTemplates::get_repartition_template_by_title($_POST["title"], $_POST["tricount"]);
            if ($template_title)
                $res = "false";
        }
        else
            $this->redirect();
        echo $res;
    }
}
