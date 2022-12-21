<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";
require_once "model/Operation.php";

class Template extends Model{

    public function __construct(String $title, int $tricount, ?int $id = 0) {
        
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
}