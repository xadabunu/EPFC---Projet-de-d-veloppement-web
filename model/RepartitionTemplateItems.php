<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";
require_once "model/Operation.php";
require_once "model/User.php";
require_once "model/RepartitionTemplates.php";

class RepartitionTemplateItems extends Model
{

    public function __construct(public int $weight, public User $user, public RepartitionTemplates $repartition_template)
    {
    }

// --------------------------- Get ------------------------------------ 

    public function get_repartition_template_items(): array
    {
        $array = [];
        $query = self::execute("SELECT * FROM repartition_template_items WHERE repartition_template = :id", ["id" => $this->repartition_template->id]);
        $data = $query->fetchAll();
        foreach ($data as $template_item) {
            $array[] = new RepartitionTemplateItems($template_item["weight"], User::get_user_by_id($template_item["user"]), RepartitionTemplates::get_repartition_template_by_id($template_item['repartition_template']));
        }
        return $array; 
    }

    public static function get_repartition_template_items_by_repartition_template_id(int $id): array
    {
        $array = [];
        $query = self::execute("SELECT * FROM repartition_template_items WHERE repartition_template = :id", ["id" => $id]);
        $data = $query->fetchAll();
        foreach ($data as $template_item) {
            $array[] = new RepartitionTemplateItems($template_item["weight"], User::get_user_by_id($template_item["user"]), RepartitionTemplates::get_repartition_template_by_id($template_item['repartition_template']));
        }
        return $array;
    }

    public static function get_repartition_template_items_by_repartition_template_and_user(RepartitionTemplates $repartition_template, User $user): RepartitionTemplateItems
    {
        $query = self::execute("SELECT * FROM repartition_template_items WHERE repartition_template = :id AND user = :user", ["id" => $repartition_template->id, 'user' => $user->id]);
        $data = $query->fetch();
        return new RepartitionTemplateItems($data['weight'], User::get_user_by_id($data['user']), RepartitionTemplates::get_repartition_template_by_id($data['repartition_template']));
    }

// --------------------------- Validate && Persist ------------------------------------ 

    public function persist_repartition_template_items_with_list(array $list): void
    {
        $this->delete_repartition_template_items();
        $array = array_keys($list);
        foreach ($array as $id) {
            if($this->user->id == $id){
                self::execute("INSERT INTO repartition_template_items(user, repartition_template, weight) VALUES(:user, :repartition_template, :weight)", ['user' => $id, 'repartition_template' => $this->repartition_template->id, 'weight' => $list[$id]]);
            }
        }
    }

    public function persist_repartition_template_items(): void
    {
        self::execute("INSERT INTO repartition_template_items(user, repartition_template, weight) VALUES(:user, :repartition_template, :weight)", ['user' => $this->user->id, 'repartition_template' => $this->repartition_template->id, 'weight' => $this->weight]);
    }

// --------------------------- Delete Template ------------------------------------ 

    public function delete_repartition_template_items(): void
    {
        self::execute("DELETE FROM repartition_template_items WHERE repartition_template= :id", ["id" => $this->repartition_template->id]);
    }

    public static function delete_repartition_template_items_0(RepartitionTemplates $repartition_template)
    {
        self::execute("DELETE FROM repartition_template_items WHERE repartition_template= :id", ["id" => $repartition_template->id]);
    }

}