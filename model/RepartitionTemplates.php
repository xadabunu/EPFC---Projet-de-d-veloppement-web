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

    public static function get_all_repartition_templates_by_tricount_id(int $id): array
    {
        $query = self::execute("SELECT * FROM repartition_templates WHERE tricount = :id ORDER BY title ASC", ["id" => $id]);
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $repartition_template) {
            $array[] = new RepartitionTemplates($repartition_template['title'], Tricount::get_tricount_by_id($repartition_template['tricount']), $repartition_template['id']);
        }
        return $array;
    }

    public static function get_repartition_template_by_id(int $id): RepartitionTemplates
    {
        $query = self::execute("SELECT * FROM repartition_templates WHERE id = :id", ["id" => $id]);
        $data = $query->fetch();
        if (!$data)
            Tools::abort("Invalid or missing argument.");
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

    public static function get_template_data_by_title_and_tricount(string $title, int $tricountId): array | false
    {
        $query = self::execute("SELECT * FROM repartition_templates WHERE title = :title AND tricount = :tricountId", ["title" => $title, "tricountId" => $tricountId]);
        $data = $query->fetch();
        return $data;
    }

    public static function get_repartition_template_by_title(string $title): RepartitionTemplates | false
    {
        $query = self::execute("SELECT * FROM repartition_templates WHERE title = :title", ["title" => $title]);
        $data = $query->fetch();
        if ($data != null) {
            $res = new RepartitionTemplates($data['title'], Tricount::get_tricount_by_id($data['tricount']), $data['id']);
        }
        else {
            $res = false;
        }
        return $res;
    }


// --------------------------- Validate && Persist ------------------------------------ 


    public function validate_repartition_template(): array
    {
        $errors = [];
        if (strlen($this->title) < 3 || strlen($this->title) > 256) {
            $errors['template_length'] = "Title length must be between 3 and 256.";
        }

        $data = self::get_template_data_by_title_and_tricount($this->title, $this->tricount->id);
        if ($data ? (empty($data) ? false : ( $this->id  == 0 ? true : ($data['id'] == $this->id ? false : true))) : false) {
            $errors['duplicate_title'] = "Title already exists in this tricount.";
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
        foreach($list as $key => $value) {
            $repartition_template_items = new RepartitionTemplateItems((int) $value, User::get_user_by_id($key), $repartition_template);
            $repartition_template_items->persist_repartition_template_items();
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
        foreach( $repartition_template_items as $items) {
            if ($items->user->id == $user->id) {
                return true;
            }
        }
        return false;
    }
}