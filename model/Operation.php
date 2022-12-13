<?php

require_once "framework/Model.php";

class Operation extends Model
{
    public function __construct(public string $title, public int $tricount, public float $amount, public string $operation_date, public int $initiator, public string $created_at, public ?int $id) {}
}