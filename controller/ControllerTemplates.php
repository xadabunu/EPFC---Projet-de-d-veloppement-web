<?php

require_once "controller/MyController.php";
require_once "model/User.php";
require_once "model/Tricount.php";
require_once "model/RepartitionTemplateItems.php";
require_once "model/RepartitionTemplates.php";

class ControllerTemplates extends MyController
{

// --------------------------- Index + Manage Template ------------------------------------ 


    public function index(): void
    {
        $this->redirect();
    }

    public function manage_templates(): void
    {
        if (isset($_GET["param1"]) && is_numeric($_GET["param1"])) {
            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            if (!$tricount->has_access($user))
                $this->redirect();
            (new View('templates'))->show(['tricount' => $tricount]);
        } else {
            Tools::abort("Invalid or missing argument.");
        }
    }


// --------------------------- Add/Edit Template && Add Template depuis Operation + Private valid field ------------------------------------ 


    public function add_template(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])){
            $list = [];
            $errors = [];
            $repartition_template = '';

            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            if (!$tricount->has_access($user))
                $this->redirect();

            if (isset($_POST['title'])) {
                $title = $_POST['title'];
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST, $tricount));
                
                $repartition_template = new RepartitionTemplates($title, $tricount);
                $errors = array_merge($errors, $repartition_template->validate_repartition_template());
                
                if (count($errors) == 0) {
                    $repartition_template->persist_template();
                    foreach($list as $key => $value){
                        $repartition_template_items = new RepartitionTemplateItems((int) $value, User::get_user_by_id($key), $repartition_template);
                        $repartition_template_items->persist_repartition_template_items();
                   }
                    $this->redirect('templates', 'manage_templates', $tricount->id);
                }
                
            }
            (new View('add_template'))->show(['list'=> $list,'tricount' => $tricount,'errors' => $errors, 'template' => $repartition_template]);
        }
        else{
            Tools::abort('Invalid or missing argument.');
        }
    }

    public function edit_template(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1']) && isset($_GET['param2']) && is_numeric($_GET['param2'])){
            $list = [];
            $errors = [];

            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            if (!$tricount->has_access($user))
                $this->redirect();
            if (!in_array($_GET['param2'], RepartitionTemplates::get_all_template_ids())) {
                $this->redirect();
            }
            $repartition_template = RepartitionTemplates::get_repartition_template_by_id($_GET["param2"]);
            if ($repartition_template->tricount->id != $_GET['param1']) {
                $this->redirect();
            }

            if (isset($_POST['title'])) {
                $title = $_POST['title'];
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST, $tricount));
                $repartition_template->title = $title;
                $errors = array_merge($errors, $repartition_template->validate_repartition_template());

                if (count($errors) == 0) {
                    $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($repartition_template->id);
                    foreach($repartition_template_items as $items){
                        $items->delete_repartition_template_items();
                    }
                    foreach($list as $key => $value){
                         $repartition_template_items = new RepartitionTemplateItems((int) $value, User::get_user_by_id($key), $repartition_template);
                         $repartition_template_items->persist_repartition_template_items();
                    }
                    $repartition_template->persist_template();
                    $this->redirect('templates', 'manage_templates', $tricount->id);
                }
            }
            (new View('edit_template'))->show(['list'=> $list,'tricount' => $tricount, 'errors' => $errors,
             'template' => $repartition_template]);
        }
        else{
            Tools::abort('Invalid or missing argument');
        }
    }    

    private function is_valid_fields(array $array, Tricount $tricount): array
    {
        $errors = [];
        if (empty($array['title'])) {
            $errors['empty_title'] = "Title is required";
        }

        $numberChecked = self::get_whom($array, $tricount);

        if(count($numberChecked) == 0 ){
            $errors['whom'] = "You must choose at least one person";
        }

        $id = [];
        foreach($numberChecked as $user){
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

        return $errors;
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


// --------------------------- Delete + ConfirmDelete Template ------------------------------------ 


    public function delete_template(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1']) && isset($_GET['param2']) && is_numeric($_GET['param2'])) {
            if (!in_array($_GET['param1'], RepartitionTemplates::get_all_template_ids())) {
                $this->redirect();
            }
            $repartition_template = RepartitionTemplates::get_repartition_template_by_id($_GET["param1"]);      
            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param2"]);
            if($repartition_template->tricount->id != $tricount->id) {
                $this->redirect();
            }
            if (!$tricount->has_access($user))
                $this->redirect();
            (new View('delete_template'))->show(['template' => $repartition_template, 'tricount' => $tricount]);
        } else {
            Tools::abort("Invalid or missing argument");
        }
    }

    public function confirm_delete_template(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $repartition_template = RepartitionTemplates::get_repartition_template_by_id($_GET["param1"]);      
            $user = $this->get_user_or_redirect();
            $tricount = $repartition_template->tricount;
            if (!$tricount->has_access($user))
                $this->redirect();
            RepartitionTemplateItems::delete_repartition_template_items_with_object($repartition_template);
            $repartition_template->delete_repartition_template();
            $this->redirect('templates', 'manage_templates', $repartition_template->tricount->id);
        } else {
            Tools::abort("Invalid or missing argument");
        }
    }
}
