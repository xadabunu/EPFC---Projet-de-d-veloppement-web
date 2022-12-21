<?php

require_once "framework/Model.php";

class Operation extends Model
{
    public function __construct(public string $title, public int $tricount, public float $amount, public string $operation_date, public int $initiator, public string $created_at, public ?int $id) {}

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
}

