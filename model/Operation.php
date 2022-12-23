<?php

require_once "framework/Model.php";

class Operation extends Model
{
    public function __construct(public string $title, public int $tricount, public float $amount, public string $operation_date, public int $initiator, public string $created_at, public ?int $id = 0) {}

    public static function get_operation_by_id(int $id): Operation
    {
        $query = self::execute("SELECT * FROM operations WHERE id = :id", ["id" => $id]);
        $data = $query->fetch();
        return new Operation($data['title'], $data['tricount'], $data['amount'], $data['operation_date'], $data['initiator'], $data['created_at'], $data['id']);
    }

    public function get_participants() : array
    {
        $query = self::execute("SELECT u.* FROM users u join repartitions r on u.id = r.user WHERE r.operation = :id", ["id" => $this->id]);
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $user) {
            $array[] = new User($user['email'], $user['hashed_password'], $user['full_name'], $user['role'], $user['iban'], $user['id']);
        }
        return $array;
    }

    public function get_previous(): int | null
    {
        $query = self::execute("WITH tmp AS (SELECT * FROM operations WHERE tricount = :tricount_id ORDER BY created_at)
                        select prev_id
                        from ( select id, 
                            lag(id) over (ORDER BY created_at) as prev_id,
                            from tmp) as t where id = :op_id", ["tricount_id" => $this->tricount,
                                                                "op_id" => $this->id]);
        return ($query->fetch())['prev_id'];
    }

    public function get_next(): int | null
    {
        $query = self::execute("WITH tmp AS (SELECT * FROM operations WHERE tricount = :tricount_id ORDER BY created_at)
                        select next_id
                        from ( select id, 
                            lead(id) over (ORDER BY created_at) as next_id,
                            from tmp) as t where id = :op_id", ["tricount_id" => $this->tricount,
                                                                "op_id" => $this->id]);
        return ($query->fetch())['next_id'];
    }

    public function persist_operation() : Operation {
        if($this->id != 0){
            self::execute("UPDATE operations SET title= :title, tricount= :tricount, amount= :amount, operation_date= :operation_date, initiator= :initiator, created_at= :created_at WHERE id= :id",
                            ["title"=>$this->title, 'tricount'=>$this->tricount, 'amount'=>$this->amount, 'operation_date'=>$this->operation_date,
                            'initiator'=>$this->initiator, 'created_at'=>$this->created_at, 'id'=>$this->id]);
        }
        else {
        self::execute("INSERT INTO operations(title, tricount, amount, operation_date, initiator, created_at) VALUES(:title, :tricount, :amount, :operation_date, :initiator, :created_at)",
                        ["title"=>$this->title, 'tricount'=>$this->tricount, 'amount'=>$this->amount, 'operation_date'=>$this->operation_date, 'initiator'=>$this->initiator, 'created_at'=>$this->created_at]);
        }
        $this->id = Model::lastInsertId();
        return $this;
    }

    public function get_subscriptors() : array {
        $query = self::execute("SELECT DISTINCT users.* FROM users, subscriptions, tricounts WHERE subscriptions.user = users.id AND subscriptions.tricount= :id",
                                ['id'=> $this->id]);
        $data = $query->fetchAll();
        $array = [];
		foreach ($data as $user) {
			$array[] = new User($user['mail'], $user['hashed_password'], $user['full_name'], $user['role'], $user['iban'], $user['id']);
		}
		return $array;
    }

    public function validate_operations() : array {
        $errors = [];
        if(!strlen($this->title) >0){
            $errors['required'] = "Title is required.";
        }
        if(!(strlen($this->title) >= 3)){
            $errors['lenght'] = "Title length must be higher than 3.";
        }
        if(($this->amount) <= 0 || empty($this->amount) ){
            $errors['amount'] = "Amount is required and must be positive";
        }
        return $errors;
    }
    
}

