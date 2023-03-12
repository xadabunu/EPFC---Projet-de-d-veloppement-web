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

    public static function get_repartitions_by_operation_id(int $id): array
    {
        $query = self::execute("SELECT * FROM repartitions WHERE operation = :id", ["id" => $id]);
        $data = $query->fetchAll();
        $list = [];
        foreach ($data as $var) {
            $list[$var['user']] = $var['weight'];
        }
        return $list;
    }

// --------------------------- Delete ------------------------------------ 

    public static function delete_repartitions_by_operation_id(int $id): void
    {
        self::execute("DELETE FROM repartitions WHERE operation= :id", ["id" => $id]);
    }
    
}