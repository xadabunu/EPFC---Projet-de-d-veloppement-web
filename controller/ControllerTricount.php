<?php

require_once "model/User.php";
require_once "model/Tricount.php";
require_once "controller/MyController.php";

class ControllerTricount extends MyController
{

// --------------------------- Index + Operations && Balance du Tricount ------------------------------------


    public function index(): void
    {
        $this->redirect("user", "my_tricounts");
    }

    public function tricount_exists_service(): void {
        $rez = "false";
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $user = $this->get_user_or_redirect();
            $tricounts = $user->get_created_tricounts();
            foreach ($tricounts as $tri) {
                if($tri->title === $_GET["param1"]){
                    $rez = "true";
                }
            }
        }
        echo $rez;    
    }

    public function operations(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
                $this->redirect();
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            if (!$tricount->has_access($user))
                $this->redirect();
            $list = $tricount->get_operations();
            $total = $tricount->get_total_expenses();
            $user_total = $tricount->get_user_total($user->id);
            $alone = $tricount->get_number_of_participants() == 1;
            $operations_json = $tricount->get_operation_as_json();
            (new View("tricount"))->show([
                "list" => $list,
                "tricount" => $tricount,
                "total" => $total,
                "user_total" => $user_total,
                "alone" => $alone,
                "operations_json" => $operations_json
            ]);
        } else {
            Tools::abort("Invalid or missing argument.");
        }
    }

    public function balance(): void
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
                $this->redirect();
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            if (!$tricount->has_access($user))
                $this->redirect();
            $list = $tricount->get_subscriptors_with_creator();
            $amounts = [];
            foreach ($list as $sub) {
                $amounts[$sub->id] = $tricount->get_balance($sub->id);
            }
            (new View("balance"))->show([
                "user" => $user,
                "subs" => $list,
                "amounts" => $amounts,
                "tricount" => $tricount,
                "max" => max(array_map("abs", $amounts))
            ]);
        } else {
            Tools::abort("Invalid or missing argument.");
        }
    }


// --------------------------- Add/Edit Tricount && Add Subs ------------------------------------ 


    public function add_tricount(): void
    {
        $title = '';
        $created_at = '';
        $creator = '';
        $description = '';
        $errors = [];

        if (isset($_POST['title']) && isset($_POST['description'])) {
            $user = $this->get_user_or_redirect();
            $title = $_POST['title'];
            $description = $_POST['description'];
            $creator = MyController::get_user_or_redirect();
            $created_at = Date("Y-m-d H:i:s");
            $tricount = new Tricount($title, $created_at, $creator, $description);
            $errors = array_merge($errors, $tricount->validate());
            if (count($errors) == 0) {
                $tricount->persist_tricount();
                $this->redirect('tricount', 'operations', $tricount->id);
            }
        }
        (new View('add_tricount'))->show(["title" => $title, "description" => $description, "errors" => $errors]);
    }

    public function edit_tricount(): void
    {
        $errors = [];

        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
                $this->redirect();
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            if (!$tricount->has_access($user))
                $this->redirect();

            if (isset($_POST['title']) || isset($_POST['description'])) {
                $tricount->title = $_POST['title'];
                $tricount->description = $_POST['description'];
                $errors = array_merge($errors, $tricount->validate());

                if (count($errors) == 0) {
                    $tricount->persist_tricount();
                    $this->redirect('tricount', 'operations', $tricount->id);
                }
            }
            (new View("edit_tricount"))->show([
                'tricount' => $tricount,
                'errors' => $errors,
            ]);
        } else {
            Tools::abort("Invalid or missing argument.");
        }
    }

    public function add_subscriptors(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
                $this->redirect();
            if (isset($_POST['subscriptor'])) {
                $user = $this->get_user_or_redirect();
                $subscriptor = $_POST['subscriptor'];
                $tricount = Tricount::get_tricount_by_id($_GET['param1']);
                if (!$tricount->has_access($user))
                    $this->redirect();
                $tricount->persist_subscriptor($subscriptor);
                $this->redirect('tricount', 'edit_tricount', $_GET['param1']);
            } else {
                $this->redirect('tricount', 'edit_tricount', $_GET['param1']);
            }

        } else
            Tools::abort("Invalid or missing argument.");
    }

    public function add_subscriptor_service(): void
    {
        Tricount::get_tricount_by_id($_GET['param1'])->persist_subscriptor($_POST["id"]);
    }

// --------------------------- Delete + ConfirmDelete Tricount && Delete Subs ------------------------------------ 


    public function delete_subscriptor(): void
    {
        if (isset($_POST['subscriptor_name'])) {
            $user = $this->get_user_or_redirect();
            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
                $this->redirect();
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            if (!$tricount->has_access($user))
                $this->redirect();
            $subscriptor = $_POST['subscriptor_name'];
            $tricount->delete_subscriptor($subscriptor);
            $this->redirect('tricount', 'edit_tricount', $_GET['param1']);
        }
    }

    public function delete_tricount(): void
    {
        if(isset($_GET['param1']) && is_numeric($_GET['param1'])){
        $user = $this->get_user_or_redirect();
        if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
            $this->redirect();
        $tricount = Tricount::get_tricount_by_id($_GET['param1']);
        if (!$tricount->has_access($user))
            $this->redirect();
        (new View("delete_tricount"))->show(['tricount' => $tricount]);
        }
        else{
            Tools::abort("Invalid or missing argument");
        }
    }    

    public function confirm_delete_tricount(): void
    {
        $user = $this->get_user_or_redirect();
        if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
            $this->redirect();
        $tricount = tricount::get_tricount_by_id($_GET['param1']);
        if (!$tricount->has_access($user))
            $this->redirect();
        $tricount->delete_tricount_cascade();
        $this->redirect('user', 'index');
    }

    public function delete_subscriptor_service(): void
    {
        $user = $this->get_user_or_redirect();
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);            
            if (!$tricount || !$tricount->has_access($user) || !isset($_POST["id"]) || !is_numeric($_POST["id"])) {
                echo "false";
                return ;
            }
            $tricount->delete_subscriptor($_POST["id"]);
            echo "true";
        } else
            echo "false";
    }
}
