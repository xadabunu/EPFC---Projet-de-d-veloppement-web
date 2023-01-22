<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";
require_once "model/Operation.php";

class Template extends Model{

    public function __construct(public String $title, public int $tricount, public ?int $id = 0) {
        
    }

    public static function get_templates(int $id): array { 
            $query = self::execute("SELECT * FROM repartition_templates WHERE tricount = :id", ["id" => $id]);
            $data = $query->fetchAll();
            $array = [];
            foreach ($data as $template) {
                $array[] = new Template($template['title'], $template['tricount'], $template['id']);
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
    
    
}