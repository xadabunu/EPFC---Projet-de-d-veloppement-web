<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";
require_once "model/Operation.php";

class Repartitions extends Model
{

    public function __construct(public int $weight, public Operation $operation, public User $user)
    {  
    }

// --------------------------- Get ------------------------------------ 

  
    public static function get_all_repartitions_by_operation_id(int $id): array
    {
        $query = self::execute("SELECT * FROM repartitions WHERE operation = :id", ["id" => $id]);
        $data = $query->fetchAll();
        $list = [];
        foreach ($data as $var) {
            $list[] = new Repartitions($var['weight'], Operation::get_operation_by_id($var['operation']), User::get_user_by_id($var['user']));
        }
        return $list;
    }

    public static function get_repartition_by_operation_and_user_id(int $operation_id, User $user): Repartitions
    {
        $query = self::execute("SELECT * FROM repartitions WHERE operation = :operation AND user = :user", ["operation" => $operation_id, "user" => $user->id]);
        $data = $query->fetch();
        return new Repartitions($data['weight'], Operation::get_operation_by_id($operation_id), $user);
    }


// --------------------------- Delete ------------------------------------ 

    public static function delete_repartitions_by_operation_id(int $id): void
    {
        self::execute("DELETE FROM repartitions WHERE operation= :id", ["id" => $id]);
    }
    
}