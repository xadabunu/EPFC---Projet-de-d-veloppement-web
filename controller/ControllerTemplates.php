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
        $repartition_templates = [];
        $tricount = "";
        $all_templates_items = [];
        $all_templates_items_for_view = [];
        $all_weight_total = [];
        $UsernameWeight = [];

        if (isset($_GET["param1"]) && is_numeric($_GET["param1"])) {
            $user = $this->get_user_or_redirect();
            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
                $this->redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            if (!$tricount->has_access($user))
                $this->redirect();
            $repartition_templates = RepartitionTemplates::get_all_repartition_templates_by_tricount_id($tricount->id);
            foreach ($repartition_templates as $template) {
                $template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($template->id);
                
                $templateUserWeightList = [];
                $array_repartition_template_items = $template_items->get_repartition_template_items();
                foreach($array_repartition_template_items as $item){
                    $templateUserWeightList[$item->user->id] =  $item->weight;
                }
                $all_templates_items[] = $templateUserWeightList;
            }
            foreach ($all_templates_items as $template_item) {
                $poids = 0;
                foreach ($template_item as $key => $Value) {
                    $tmpUser = User::get_user_by_id($key)->full_name;
                    $UsernameWeight[$tmpUser] = $Value;
                    $poids += $Value;
                }
                ksort($UsernameWeight);
                $all_templates_items_for_view[] = $UsernameWeight;
                $UsernameWeight = [];
                $all_weight_total[] = $poids;
            }
            (new View('templates'))->show(['templates' => $repartition_templates, 'tricount' => $tricount, 'all_templates_items_for_view' => $all_templates_items_for_view, 'all_weight_total' => $all_weight_total]);
        } else {
            Tools::abort("Invalid or missing argument.");
        }
    }


// --------------------------- Add/Edit Template && Add Template depuis Operation + Private valid field ------------------------------------ 


    public function add_template(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])){
            $list = '';
            $errors = [];
            $userChecked = [];
            $userWeight = [];

            $user = $this->get_user_or_redirect();
            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id()))
                $this->redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            if (!$tricount->has_access($user))
                $this->redirect();
            $subscriptors = $tricount->get_subscriptors_with_creator();

            foreach($subscriptors as $subscriptor){
                $userChecked[$subscriptor->id] = 'unchecked';
                $userWeight[$subscriptor->id] = '1';
            }

            if (isset($_POST['title'])) {
                $title = Tools::sanitize($_POST['title']);
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST, $tricount));

                if (count($errors) == 0) {
                    $repartition_template = new RepartitionTemplates($title, $tricount);
                    $errors = $repartition_template->validate_repartition_template();
                    
                    if (count($errors) == 0) {
                        $repartition_template->persist_template();
                        $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($repartition_template->id);
                        $repartition_template_items->persist_repartition_template_items($repartition_template, $list);
                        $this->redirect('templates', 'manage_templates', $tricount->id);
                    }
                }
            }
            (new View('add_template'))->show(['list'=> $list,'tricount' => $tricount, 'subscriptors' => $subscriptors,
             'errors' => $errors, 'userChecked' => $userChecked, 'userWeight' => $userWeight
        ]);
        }
        else{
            Tools::abort('Invalid or missing argument.');
        }
    }

    public function edit_template(): void
    {

        if (isset($_GET['param1']) && is_numeric($_GET['param1']) && isset($_GET['param2']) && is_numeric($_GET['param2'])){
            $list = '';
            $errors = [];
            $userChecked = [];
            $userWeight = [];

            if (!in_array($_GET['param1'], Tricount::get_all_tricounts_id())) {
                $this->redirect();
            }
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
            $subscriptors = $tricount->get_subscriptors_with_creator();
            $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($repartition_template->id);

            $repartition_template_items_choosen = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($repartition_template->id);

            $templateUserWeightList = [];
            $array_repartition_template_items = $repartition_template_items_choosen->get_repartition_template_items();
            foreach($array_repartition_template_items as $item){
                $templateUserWeightList[$item->user->id] =  $item->weight;
            }

            foreach($subscriptors as $subscriptor){
                $userChecked[$subscriptor->id] = array_key_exists($subscriptor->id, $templateUserWeightList) ? 'checked' : 'unchecked';
                $userWeight[$subscriptor->id] = array_key_exists($subscriptor->id, $templateUserWeightList) ? $templateUserWeightList[$subscriptor->id] : '1';
            }

            if (isset($_POST['title'])) {
                $title = Tools::sanitize($_POST['title']);
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST, $tricount));
                $repartition_template->title = $title;
                $errors = array_merge($errors, $repartition_template->validate_repartition_template());

                if (count($errors) == 0) {
                    $repartition_template_items->persist_repartition_template_items($repartition_template, $list);
                    $repartition_template->persist_template();
                    $this->redirect('templates', 'manage_templates', $tricount->id);
                }
            }
            (new View('edit_template'))->show(['list'=> $list,'tricount' => $tricount, 'subscriptors' => $subscriptors, 'errors' => $errors,
             'template' => $repartition_template, 'userChecked' => $userChecked, 'userWeight' => $userWeight
            ]);
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
                if(in_array(substr($key, 7, 1), $id)){
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
            if (!in_array($_GET['param2'], Tricount::get_all_tricounts_id())) {
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
            $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($repartition_template->id);
            $repartition_template_items->delete_repartition_template_items();
            $repartition_template->delete_repartition_template();
            $this->redirect('templates', 'manage_templates', $repartition_template->tricount->id);
        } else {
            Tools::abort("Invalid or missing argument");
        }
    }
}
