<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";
require_once "model/Operation.php";

class RepartitionTemplateItems extends Model
{

    public function __construct(public int $weight, public User $user, public ?int $id = 0)
    {
    }

    public function get_repartition_template_items(): array
    {
        $array = [];
        $query = self::execute("SELECT * FROM repartition_template_items WHERE repartition_template = :id", ["id" => $this->id]);
        $data = $query->fetchAll();
        foreach ($data as $template_item) {
            $array[] = new RepartitionTemplateItems($template_item["weight"], User::get_user_by_id($template_item->user));
        }
        return $array; 
    }

    public function get_repartition_items(): array
    {
        $array = [];
        $query = self::execute("SELECT * FROM repartition_template_items WHERE repartition_template = :id", ["id" => $this->id]);
        $data = $query->fetchAll();
        foreach ($data as $template_item) {
            $array[$template_item["user"]] =  $template_item["weight"];
        }
        return $array;
    }

    public static function get_repartition_template_items_by_repartition_template_id(int $id): RepartitionTemplateItems
    {
        $query = self::execute("SELECT * FROM repartition_template_items WHERE repartition_template = :id", ["id" => $id]);
        $data = $query->fetch();
        return new RepartitionTemplateItems($data['weight'],User::get_user_by_id($data['user']), $data['repartition_template']);
    }

// --------------------------- Validate && Persist ------------------------------------ 


    public function persist_repartition_template_items(RepartitionTemplates $repartition_template, array $list): void
    {
        $this->delete_repartition_template_items();
        $array = array_keys($list);
        foreach ($array as $id) {
            self::execute("INSERT INTO repartition_template_items(user, repartition_template, weight) VALUES(:user, :repartition_template, :weight)", ['user' => $id, 'repartition_template' => $repartition_template->id, 'weight' => $list[$id]]);
        }
    }

// --------------------------- Delete Template ------------------------------------ 


    public function delete_repartition_template_items(): void
    {
        self::execute("DELETE FROM repartition_template_items WHERE repartition_template= :id", ["id" => $this->id]);
    }

    

}