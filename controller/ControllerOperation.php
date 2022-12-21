<?php

require_once "controller/MyController.php";

class ControllerOperation extends MyController
{
    public function index(): void
    {
    }

    public function details(): void
    {
        if (isset($_GET['param1'])) {
            $user = $this->get_user_or_redirect();
            $op = Operation::get_operation_by_id($_GET['param1']);
            $participants_list = $op->get_participants();
            $initiator = User::get_user_by_id($op->initiator);
            $prev = $op->get_previous();
            $next = $op->get_next();
            (new View("operation"))->show(["user" => $user,
                                            "operation" => $op,
                                            "participants_list" => $participants_list,
                                            "initiator" => $initiator,
                                            "next" => $next,
                                            "previous => $prev"]);
        }
        else {
            Tools::abort("Invalid or missing argument");
        }
    }
}