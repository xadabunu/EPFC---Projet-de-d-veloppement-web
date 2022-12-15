<?php

require_once "framework/Model.php";
require_once "model/Operation.php";

class Tricount extends Model
{

    public function __construct(public String $title, public String $created_at, public int $creator, public ?String $description = null,  public ?int $id = 0)
    {
    }

    public static function get_tricounts_list(int $id): array
    {
        $query = self::execute("SELECT t.* FROM tricounts t JOIN subscriptions s ON t.id = s.tricount WHERE s.user = :id", ["id" => $id]);
        $data = $query->fetchAll();

        $array = [];
        foreach ($data as $tricount) {
            $array[] = new Tricount($tricount['title'], $tricount['created_at'], $tricount['creator'], $tricount['description'], $tricount['id']);
        }
        return $array;
    }

    public static function get_tricount_by_id(int $id): Tricount
    {
        $query = self::execute("SELECT * FROM tricounts WHERE id = :id", ["id" => $id]);
        $data = $query->fetch();
        return new Tricount($data['title'], $data['created_at'], $data['creator'], $data['description'], $data['id']);
    }

    public function get_number_of_participants(): int
    {
        $query = self::execute("SELECT COUNT(*) as number FROM subscriptions WHERE tricount = :id", ["id" => $this->id]);
        return ($query->fetch())['number'];
    }

    public function get_operations(): array
    {
        $query = self::execute("SELECT * FROM operations WHERE tricount = :id", ["id" => $this->id]);
        $data = $query->fetchAll();
        $array = [];
        foreach($data as $op)
        {
            $array[] = new Operation($op['title'], $op['tricount'], $op['amount'], $op['operation_date'], $op['initiator'], $op['created_at'], $op['id']);
        }
        return $array;
    }

    public function persist_tricount() : Tricount{
        self::execute("INSERT INTO tricounts(title, description, created_at, creator) VALUES(:title, :description, :created_at, :creator)",
                        ["title"=>$this->title, "description"=>$this->description, "created_at"=>date("Y-m-d H:i:s"), "creator"=>$this->creator]);
        $this->id = Model::lastInsertId();
        return $this;
    }

    public static function lastTricountId() : String {
        $id = Model::lastInsertId();
        return $id;
    }

    public function validate() : array{
        $errors = [];
        if(!strlen($this->title) >0){
            $errors[] = "Title is required.";
        }
        if(!(strlen($this->title) >= 3)){
            $errors[] = "Title length must be higher than 3.";
        }
        if(strlen($this->description) > 0 && !(strlen($this->description) >=3)){
            $errors[] = "Description length must be higher than 3.";
        }
        return $errors;
    }
}
