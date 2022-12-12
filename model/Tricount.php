<?php

require_once "framework/Model.php";

class Tricount extends Model {

    public function __construct(public String $title, public String $created_at, public int $creator, public int $id = 0, public ?String $description = null){}

    public static function get_tricounts_list(int $id) : array
    {
        $query = self::execute("SELECT t.* FROM tricounts t JOIN subscriptions s ON t.id = s.tricount WHERE s.user = :id", ["id" => $id]);
        $data = $query -> fetchAll();

        $array = [];
        foreach ($data as $tricount) {
            $array[] = new Tricount($tricount['title'], $tricount['created_at'], $tricount['creator'], $tricount['id'], $tricount['description']);
        }
        return $array;
    }

    public function get_number_of_participants() : int
    {
        $query = self::execute("SELECT COUNT(*) as number FROM subscriptions WHERE tricount = :id", ["id" => $this->id]);
        return ($query -> fetch())['number'];
    }

}