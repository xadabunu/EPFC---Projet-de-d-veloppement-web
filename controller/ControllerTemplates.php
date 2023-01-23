<?php 

require_once "controller/MyController.php";
require_once "model/Template.php";
require_once "model/User.php";


class ControllerTemplates extends MyController{

    public function index(): void {}


    public function manage_templates(): void {
        $templates = [];
        $tricount = "";
        $all_templates_items = [];
        $all_templates_items_for_view = [];
        $all_weight_total = [];
        $UsernameWeight = [];
        

        if(isset($_GET["param1"])){
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            $templates = Template::get_templates($tricount->id);
            foreach($templates as $template){
                $all_templates_items[] = $template->get_repartition_items();
            }

        

            foreach($all_templates_items as $template_item){
                $poids = 0;
                foreach($template_item as $key => $Value){
                    $tmpUser = User::get_user_by_id($key)->full_name;
                    $UsernameWeight[$tmpUser] = $Value;
                    $poids += $Value;
                }
                ksort($UsernameWeight);
                $all_templates_items_for_view[] = $UsernameWeight;
                $UsernameWeight = [];
                $all_weight_total[] = $poids;
            }

            (new View('templates'))->show(['templates'=>$templates, 'tricount'=>$tricount, 'all_templates_items_for_view'=>$all_templates_items_for_view, 'all_weight_total'=>$all_weight_total]);

        }
        else{Tools::abort("Invalid or missing argument.");}



    }

    public function edit_template(): void {

    
    }

    public function add_template(): void {
        $errors = [];
        $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
        $subscriptors = $tricount->get_subscriptors_with_creator();

        if(isset($_POST['title'])){
            $title = $_POST['title'];
            $list = self::get_weight($_POST, $tricount);
            $errors = array_merge($errors, self::is_valid_fields($_POST));
                
              if(count($errors) == 0){
                $template = new Template($title, $tricount);
                $errors = $template->validate_template();

                 if(count($errors) == 0){
                    $template->persist_template();
                    $template->persist_template_items($template, $list);
                    $this->redirect('templates', 'manage_templates', $tricount->id);
                }
             }
          }

    (new View('add_template'))->show(['tricount'=>$tricount, 'subscriptors'=>$subscriptors, 'errors'=>$errors]);

    }

    private function is_valid_fields(array $array) : array {
        $errors = [];
        if(empty($array['title'])){
            $errors ['empty_title'] = "Title is required";
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

    private function get_weight(array $array, Tricount $tricount) : array {
        $list = self::get_whom($array, $tricount);
        $result = [];
        foreach($list as $sub) {
            $result[$sub->id] = $array['weight_'.$sub->id];
        }
        return $result;
    }

}



?>