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
            $list = $op->get_participants();
            $amounts = [];
            foreach ($list as $participant) {
                $amounts[$participant->id] = $op->get_personnal_amount($participant->id);
            }
            /* total / sum of weight * weight of user */
            $initiator = User::get_user_by_id($op->initiator);
            $prev = $op->get_previous();
            $next = $op->get_next();
            $tricount = Tricount::get_tricount_by_id($op->tricount);
            (new View("operation"))->show(["user" => $user,
                                            "operation" => $op,
                                            "list" => $list,
                                            "initiator" => $initiator,
                                            "next" => $next,
                                            "previous" => $prev,
                                            "amounts" => $amounts,
                                            "tricount" => $tricount]);
        }
        else {
            Tools::abort("Invalid or missing argument");
        }
    }
}