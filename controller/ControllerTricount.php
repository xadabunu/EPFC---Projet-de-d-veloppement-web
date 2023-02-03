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
            (new View("tricount"))->show([
                "list" => $list,
                "tricount" => $tricount,
                "total" => $total,
                "user_total" => $user_total,
                "alone" => $alone
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

        if (isset($_POST['title'])) {
            $title = Tools::sanitize($_POST['title']);
            $description = Tools::sanitize($_POST['description']);
            $creator = MyController::get_user_or_redirect();
            $created_at = Date("Y-m-d H:i:s");
            $tricount = new Tricount($title, $created_at, $creator, $description);
            $errors = array_merge($errors, $tricount->validate());
            if (count($errors) == 0) {
                $tricount->persist_tricount();
                $this->redirect('tricount', 'operations', $tricount->id);
            }
        }
        (new View('add_tricount'))->show(["title" => $title, "desciption" => $description, "errors" => $errors]);
    }

    public function edit_tricount(): void
    {
        $subscriptors = [];
        $creator = '';
        $errors = [];
        $title = '';
        $deletables = [];

        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
                $this->redirect();
            $tricount = Tricount::get_tricount_by_id($_GET['param1']);
            if (!$tricount->has_access($user))
                $this->redirect();
            $title = $tricount->title;
            $creator = $tricount->creator;
            $subscriptors = $tricount->get_subscriptors();
            $cbo_users = $tricount->get_cbo_users();
            $deletables = $tricount->get_deletables();

            if (isset($_POST['title']) || isset($_POST['description'])) {
                $tricount->title = Tools::sanitize($_POST['title']);
                $tricount->description = Tools::sanitize($_POST['description']);
                $errors = array_merge($errors, $tricount->validate());

                if (count($errors) == 0) {
                    $tricount->persist_tricount();
                    $this->redirect('tricount', 'operations', $tricount->id);
                }
            }
            (new View("edit_tricount"))->show([
                'tricount' => $tricount, 'subscriptors' => $subscriptors, 'creator' => $creator,
                'errors' => $errors, 'cbo_users' => $cbo_users, 'title' => $title, 'deletables' => $deletables
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
                $subscriptor = Tools::sanitize($_POST['subscriptor']);
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
            $subscriptor = Tools::sanitize($_POST['subscriptor_name']);
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
}
