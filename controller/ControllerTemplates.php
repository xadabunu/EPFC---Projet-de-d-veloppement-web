<?php 

require_once "controller/MyController.php";
require_once "model/Template.php";


class ControllerTemplates extends MyController{

    public function index(): void {}


    public function manage_templates(): void {
        $templates = [];
        $tricount = "";
        $all_template_items = [];

        if(isset($_GET["param1"])){
            $tricount = Tricount::get_tricount_by_id($_GET["param1"]);
            $templates = Template::get_templates($tricount->id);
            foreach($templates as $template){
                $all_template_items = $template->get_repartition_items();
            }
        }

        (new View('templates'))->show(['templates'=>$templates, 'tricount'=>$tricount]);


    }

    public function edit_templates(): void {

    
    }

}



?>