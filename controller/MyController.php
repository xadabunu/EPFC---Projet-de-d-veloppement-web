<?php

class MyController extends Controller
{

    public function index(): void
    {
    }

    public function get_user_or_false(): User | false
    {
        return $this->user_logged() ? $_SESSION['user'] : false;
    }

    public function get_user_or_redirect(): User
    {
        $user = $this->get_user_or_false();
        if ($user === FALSE)
            $this->redirect();
        return $user;
    }
}
