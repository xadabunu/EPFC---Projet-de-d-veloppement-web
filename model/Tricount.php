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

    public function get_subscriptors() : array {
        $query = self::execute("SELECT DISTINCT users.* FROM users, subscriptions, tricounts WHERE subscriptions.user = users.id AND tricount=:id", ['id'=> $this->id]);
        $data = $query->fetchAll();
        $array = [];
		foreach ($data as $user) {
			$array[] = new User($user['mail'], $user['hashed_password'], $user['full_name'], $user['role'], $user['iban'], $user['id']);
		}
		return $array;
    }

    public function get_cbo_users() : array {
        $query = self::execute("SELECT * FROM users WHERE id != :creator_id AND id NOT IN (SELECT user FROM subscriptions WHERE tricount = :tricount_id)",
                                ['creator_id'=>$this->creator, 'tricount_id'=>$this->id]);
        $data = $query->fetchAll();
        $array = [];
        foreach($data as $user){
            $array[] = new User($user["mail"], $user["hashed_password"], $user["full_name"], $user["role"], $user["iban"], $user["id"]);
        }
        return $array;
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

    public function persist_tricount() : Tricount {
        if(self::get_tricount_by_id($this->id)){
            self::execute("UPDATE tricounts SET title =:title, description =:description WHERE id=:id",
                            ["title"=>$this->title, "description"=>$this->description, "id"=>$this->id]);
        }
        else{
        self::execute("INSERT INTO tricounts(title, description, created_at, creator) VALUES(:title, :description, :created_at, :creator)",
                        ["title"=>$this->title, "description"=>$this->description, "created_at"=>date("Y-m-d H:i:s"), "creator"=>$this->creator]);
        }
        $this->id = Model::lastInsertId();
        return $this;
    }

    public function persist_subscriptor(int $id) : void {
        self::execute("INSERT INTO subscriptions(user, tricount) VALUES(:user, :tricount)",["user"=> $id, 'tricount'=>$this->id]);
    }

    public function delete_subscriptor(int $id) : void {
        self::execute("DELETE FROM subscriptions WHERE user=:user_id AND tricount=:tricount_id ",["user_id"=> $id, "tricount_id"=>$this->id]);
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
