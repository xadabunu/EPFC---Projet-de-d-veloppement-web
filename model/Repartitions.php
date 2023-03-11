<?php

require_once "framework/Model.php";
require_once "model/Tricount.php";
require_once "model/Operation.php";

class Repartitions extends Model
{

    public function __construct(public int $weight, public Operation $operation, public User $user)
    {
        
    }
}