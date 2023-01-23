<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";
require_once "model/Operation.php";

class Template extends Model{

    public function __construct(public String $title, public Tricount $tricount, public ?int $id = 0) {
        
    }

    public static function get_templates(int $id): array { 
            $query = self::execute("SELECT * FROM repartition_templates WHERE tricount = :id", ["id" => $id]);
            $data = $query->fetchAll();
            $array = [];
            foreach ($data as $template) {
                $tricountInstance = Tricount::get_tricount_by_id($template['tricount']);
                $array[] = new Template($template['title'], $tricountInstance, $template['id']);
            }
            return $array;
        
    }

    public function get_repartition_items(): array{
        $array = [];

        $query = self::execute("SELECT * FROM repartition_template_items WHERE repartition_template = :id", ["id" => $this->id]);
        $data = $query->fetchAll();

        foreach($data as $template_item){
            $array[$template_item["user"]] =  $template_item["weight"];
        }
        return $array;
    }

    public function validate_template(): array{
        $errors = [];

        if(strlen($this->title) < 3){
            $errors['lenght'] = "Title length must be higher than 3.";
        }
        return $errors;
    }
    

    public function persist_template(): Template {
        self::execute("INSERT INTO repartition_templates(title, tricount) VALUES(:title, :tricount)",
                        ["title"=>$this->title, 'tricount'=>$this->tricount->id]);
        
        
        $this->id = Model::lastInsertId();
        return $this;
    }

    public function persist_template_items(Template $template, array $list): void {
        $array = array_keys($list);
        foreach($array as $id){
            self::execute("INSERT INTO repartitions_template_items(user, repartition_template, weight) VALUES(:user, :repartition_template, :weight)", ['user'=>$id, 'repartition_template'=>$template->id, 'weight'=>$list[$id]]);
        }  
    }
    
}