<?php

require_once "controller/MyController.php";
require_once "model/Template.php";
require_once "model/User.php";
require_once "model/Tricount.php";

class ControllerTemplates extends MyController
{

// --------------------------- Index + Manage Template ------------------------------------ 


    public function index(): void
    {
    }

    public function manage_templates(): void
    {
        $templates = [];
        $tricount = "";
        $all_templates_items = [];
        $all_templates_items_for_view = [];
        $all_weight_total = [];
        $UsernameWeight = [];

        if (isset($_GET["param1"]) && is_numeric($_GET["param1"])) {
            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            if (!$tricount->has_access($user))
                $this->redirect();
            $templates = Template::get_templates($tricount->id);
            foreach ($templates as $template) {
                $all_templates_items[] = $template->get_repartition_items();
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
            (new View('templates'))->show(['templates' => $templates, 'tricount' => $tricount, 'all_templates_items_for_view' => $all_templates_items_for_view, 'all_weight_total' => $all_weight_total]);
        } else {
            Tools::abort("Invalid or missing argument.");
        }
    }


// --------------------------- Add/Edit Template && Add Template depuis Operation + Private valid field ------------------------------------ 


    public function add_template(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])){
            $errors = [];
            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            if (!$tricount->has_access($user))
                $this->redirect();
            $subscriptors = $tricount->get_subscriptors_with_creator();

            if (isset($_POST['title'])) {
                $title = Tools::sanitize($_POST['title']);
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST, $tricount));

                if (count($errors) == 0) {
                    $template = new Template($title, $tricount);
                    $errors = $template->validate_template();
                    
                    if (count($errors) == 0) {
                        $template->persist_template();
                        $template->persist_template_items($template, $list);
                        $this->redirect('templates', 'manage_templates', $tricount->id);
                    }
                }
            }
            (new View('add_template'))->show(['tricount' => $tricount, 'subscriptors' => $subscriptors, 'errors' => $errors]);
        }
        else{
            Tools::abort('Invalid or missing argument.');
        }
    }

    public function edit_template(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])){
            $errors = [];
            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            if (!$tricount->has_access($user))
                $this->redirect();
            $template = Template::get_template_by_template_id($_GET["param2"]);
            $subscriptors = $tricount->get_subscriptors_with_creator();
            $userAndWeightArray = $template->get_template_user_and_weight();

            if (isset($_POST['title'])) {
                $title = Tools::sanitize($_POST['title']);
                $list = self::get_weight($_POST, $tricount);
                $errors = array_merge($errors, self::is_valid_fields($_POST, $tricount));
                $template->title = $title;
                $errors = array_merge($errors, $template->validate_template());

                if (count($errors) == 0) {
                    $template->persist_template_items($template, $list);
                    $template->persist_template();
                    $this->redirect('templates', 'manage_templates', $tricount->id);
                }
            }
            (new View('edit_template'))->show(['tricount' => $tricount, 'subscriptors' => $subscriptors, 'errors' => $errors, 'template' => $template, 'list' => $userAndWeightArray]);
        }
        else{
            Tools::abort('Invalid or missing argument');
        }
    }    

    public static function add_template_from_operation(array $list, Template $template): void
    {
        $template->persist_template();
        $template->persist_template_items($template, $list);
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

        foreach($array as $key => $item){ 
            if(substr($key, 0, 6) == "weight"){
                if(!is_numeric($item) || intval($item) < 1){
                    $errors['weight'] = "Weight must be a strictly positive numeric value";
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
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $template = Template::get_template_by_template_id($_GET["param1"]);
            $user = $this->get_user_or_redirect();
            $tricount = Tricount::get_tricount_by_id($_GET["param2"]);
            if (!$tricount->has_access($user))
                $this->redirect();
            (new View('delete_template'))->show(['template' => $template, 'tricount' => $tricount]);
        } else {
            Tools::abort("Invalid or missing argument");
        }
    }

    public function confirm_delete_template(): void
    {
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $template = Template::get_template_by_template_id($_GET["param1"]);
            $user = $this->get_user_or_redirect();
            $tricount = $template->tricount;
            if (!$tricount->has_access($user))
                $this->redirect();
            $template->delete_template_items();
            $template->delete_template();
            $this->redirect('templates', 'manage_templates', $template->tricount->id);
        } else {
            Tools::abort("Invalid or missing argument");
        }
    }
}
