<?php

require_once "framework/Model.php";
require_once "model/Operation.php";
require_once "model/Template.php";

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
        $query = self::execute("SELECT DISTINCT users.* FROM users, subscriptions WHERE subscriptions.user = users.id AND tricount=:id AND subscriptions.user != :user_id",
                                ['id'=> $this->id, "user_id"=>$this->creator]);
        $data = $query->fetchAll();
        $array = [];
		foreach ($data as $user) {
			$array[] = new User($user['mail'], $user['hashed_password'], $user['full_name'], $user['role'], $user['iban'], $user['id']);
		}
		return $array;
    }

    public function get_subscriptors_with_creator() : array {
        $query = self::execute("SELECT DISTINCT users.* FROM users, subscriptions WHERE subscriptions.user = users.id AND subscriptions.tricount= :id",
                                ['id'=> $this->id]);
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
        $query = self::execute("SELECT * FROM operations WHERE tricount = :id ORDER BY created_at DESC", ["id" => $this->id]);
        $data = $query->fetchAll();
        $array = [];
        foreach($data as $op)
        {
            $array[] = new Operation($op['title'], Tricount::get_tricount_by_id($op['tricount']), $op['amount'], $op['operation_date'], User::get_user_by_id($op['initiator']), $op['created_at'], $op['id']);
        }
        return $array;
    }

    public function persist_tricount() : Tricount {
        if($this->id != 0){
            self::execute("UPDATE tricounts SET title =:title, description =:description WHERE id=:id",
                            ["title"=>$this->title, "description"=>$this->description, "id"=>$this->id]);
        }
        else {
        self::execute("INSERT INTO tricounts(title, description, created_at, creator) VALUES(:title, :description, :created_at, :creator)",
                        ["title"=>$this->title, "description"=>$this->description, "created_at"=>date("Y-m-d H:i:s"), "creator"=>$this->creator]);
        
        $this->id = Model::lastInsertId();
        self::execute("INSERT INTO subscriptions(user, tricount) VALUES (:user, :tricount)", ['user'=>$this->creator, 'tricount'=>$this->id]);
        }
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

    // public function get_repartition_templates() : array {
    //     $query = self::execute("SELECT * FROM repartition_templates WHERE tricount = :id", ["id" => $this->id]);
    //     $data = $query->fetchAll();
    //     $array = [];
    //     foreach($data as $template)
    //     {
    //         $array[] = new Template($template['title'], $template['tricount']);
    //     }
    //     return $array;
    // }

    public function delete_tricount_cascade() : void {
        $this->delete_repartition_item();
        $this->delete_repartition();
        $this->delete_template();
        $this->delete_operation();
        $this->delete_subscriptors();
        $this->delete_tricount();
        

    }

    private function delete_tricount() : void {
        self::execute("DELETE FROM tricounts WHERE id= :id ",["id"=>$this->id]);
    }

    private function delete_operation() : void {
        self::execute("DELETE FROM operations WHERE tricount= :tricount_id ",["tricount_id"=>$this->id]);
    }

    private function delete_template() : void {
        self::execute("DELETE FROM repartition_templates WHERE tricount= :tricount_id ",["tricount_id"=>$this->id]);
    }

    private function delete_repartition() : void {
        self::execute("DELETE FROM repartitions WHERE operation IN (SELECT id FROM operations WHERE tricount= :id)", ["id"=>$this->id]);
    }

    private function delete_subscriptors() : void {
        self::execute("DELETE FROM subscriptions WHERE tricount= :tricount_id", ["tricount_id"=>$this->id]);
    }

    private function delete_repartition_item() : void {
        self::execute("DELETE FROM repartition_template_items WHERE repartition_template IN (SELECT id FROM repartition_templates WHERE tricount = :tricount_id)",["tricount_id"=>$this->id]);
    }

}
