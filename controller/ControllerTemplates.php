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

            
        }

        (new View('templates'))->show(['templates'=>$templates, 'tricount'=>$tricount, 'all_templates_items_for_view'=>$all_templates_items_for_view, 'all_weight_total'=>$all_weight_total]);


    }

    public function edit_templates(): void {

    
    }

    public function add_templates(): void {

    }

}



?>