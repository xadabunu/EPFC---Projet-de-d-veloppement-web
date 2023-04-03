<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";
require_once "model/Operation.php";
require_once "model/RepartitionTemplateItems.php";

class RepartitionTemplates extends Model
{

    public function __construct(public String $title, public Tricount $tricount, public ?int $id = 0)
    {
    }

// --------------------------- Get sur les Template ------------------------------------ 

    public static function get_all_template_ids() : array 
    {
        $list = (self::execute("SELECT id FROM repartition_templates", []))->fetchAll();
        $res = [];
        foreach ($list as $var)
            $res[] = $var['id'];

        return $res;
    }     

    public static function get_all_repartition_templates_by_tricount_id(int $id): array
    {
        $query = self::execute("SELECT * FROM repartition_templates WHERE tricount = :id", ["id" => $id]);
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $repartition_template) {
            $tricountInstance = Tricount::get_tricount_by_id($repartition_template['tricount']);
            $array[] = new RepartitionTemplates($repartition_template['title'], $tricountInstance, $repartition_template['id']);
        }
        return $array;
    }

    public static function get_repartition_template_by_id(int $id): RepartitionTemplates
    {
        $query = self::execute("SELECT * FROM repartition_templates WHERE id = :id", ["id" => $id]);
        $data = $query->fetch();
        return new RepartitionTemplates($data['title'], Tricount::get_tricount_by_id($data['tricount']), $data['id']);
    }

    public static function get_repartition_template_by_id_as_json(int $id): string
    {
        $repartition_template = RepartitionTemplates::get_repartition_template_by_id($id);

        $table = [];
        $table["title"] = $repartition_template->title;
        $table["tricount"] = $repartition_template->tricount->id;
        $table["id"] = $repartition_template->id;

        return json_encode($table);
    }


// --------------------------- Validate && Persist ------------------------------------ 


    public function validate_repartition_template(): array
    {
        $errors = [];
        if (strlen($this->title) < 3 || strlen($this->title) > 256) {
            $errors['template_length'] = "Title length must be between 3 and 256.";
        }
        return $errors;
    }

    public function persist_template(): RepartitionTemplates
    {
        if ($this->id != 0) {
            self::execute(
                "UPDATE repartition_templates SET title= :title, tricount= :tricount WHERE id= :id",
                ["title" => $this->title, 'tricount' => $this->tricount->id, 'id' => $this->id]
            );
        } else {
            self::execute(
                "INSERT INTO repartition_templates(title, tricount) VALUES(:title, :tricount)",
                ["title" => $this->title, 'tricount' => $this->tricount->id]
            );
        }
        $this->id = Model::lastInsertId();
        return $this;
    }

    public function add_repartition_template_from_operation(array $list, RepartitionTemplates $repartition_template): void
    {
        $repartition_template->persist_template();
        $repartition_template_items = RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($this->id);
        foreach($repartition_template_items as $items){
            $items->persist_repartition_template_items_with_template_0($list);
        }
        
    }

// --------------------------- Delete Template ------------------------------------ 

    public function delete_repartition_template(): void
    {
        self::execute("DELETE FROM repartition_templates WHERE id= :id", ["id" => $this->id]);
    }

// --------------------------- Fonctions Template ------------------------------------ 

    public function is_participant_template(User $user): bool 
    {
        $repartition_template_items =  RepartitionTemplateItems::get_repartition_template_items_by_repartition_template_id($this->id);
        foreach( $repartition_template_items as $items){
            if($items->user->id == $user->id){
                return true;
            }
        }
        return false;
    }
    

}