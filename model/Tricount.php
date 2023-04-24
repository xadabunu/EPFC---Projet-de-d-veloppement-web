<?php

require_once "framework/Model.php";
require_once "model/Operation.php";
require_once "model/RepartitionTemplates.php";
require_once "model/RepartitionTemplateItems.php";
require_once "model/User.php";

class Tricount extends Model
{

    public function __construct(public String $title, public String $created_at, public User $creator, public ?String $description = null,  public ?int $id = 0)
    {
    }

// --------------------------- Get sur les Tricounts ------------------------------------ 


    public static function get_tricounts_list(int $id): array
    {
        $query = self::execute("SELECT t.* FROM tricounts t JOIN subscriptions s ON t.id = s.tricount WHERE s.user = :id", ["id" => $id]);
        $data = $query->fetchAll();

        $array = [];
        foreach ($data as $tricount) {
            $array[] = new Tricount($tricount['title'], $tricount['created_at'], User::get_user_by_id($tricount['creator']), $tricount['description'], $tricount['id']);
        }
        return $array;
    }

    public static function get_tricount_by_id(int $id): Tricount
    {
        $query = self::execute("SELECT * FROM tricounts WHERE id = :id", ["id" => $id]);
        $data = $query->fetch();
        return new Tricount($data['title'], $data['created_at'], User::get_user_by_id($data['creator']), $data['description'], $data['id']);
    }

    public static function get_tricount_by_title(string $title): Tricount
    {
        $query = self::execute("SELECT * FROM  tricounts WHERE title =:title", ["title" => $title]);
        $data = $query->fetch();
        return new Tricount($data['title'], $data['created_at'], User::get_user_by_id($data['creator']), $data['description'], $data['id']);
    }

    public static function get_all_tricounts_id(): array
    {
        $list = (self::execute("SELECT id FROM tricounts", []))->fetchAll();
        $res = [];

        foreach ($list as $var)
            $res[] = $var['id'];

        return $res;
    }


// --------------------------- Méthode has_access -------------------------------------------------


    public function has_access(User $user): bool
    {
        $list = $this->get_subscriptors_with_creator();
        return in_array($user, $list);
    }


// --------------------------- Get sur les dépenses && Balance ------------------------------------ 


    public function get_total_expenses(): float
    {
        $query = self::execute("SELECT SUM(amount) AS sum FROM operations WHERE tricount = :id", ["id" => $this->id]);
        $data = ($query->fetch())['sum'];
        if ($data) {
            return $data;
        }
        return 0;
    }

    public function get_user_total(int $user_id): float
    {
        $total = 0;
        $list = $this->get_operations();
        foreach ($list as $operation) {
            $total += $operation->get_user_amount($user_id);
        }
        return $total;
    }

    public function get_balance(int $user_id): float
    {
        $query = self::execute(
            "SELECT SUM(amount) AS sum FROM operations WHERE tricount = :tricount_id AND initiator = :user_id",
            [
                "tricount_id" => $this->id,
                "user_id" => $user_id
            ]
        );
        $paid = $query->fetch()['sum'];
        $spent = 0;
        $list = $this->get_operations();
        foreach ($list as $op) {
            $spent += $op->get_user_amount($user_id);
        }
        return $paid - $spent;
    }


// --------------------------- Get sur les Subs ------------------------------------ 


    public function get_number_of_participants(): int
    {
        $query = self::execute("SELECT COUNT(*) as number FROM subscriptions WHERE tricount = :id", ["id" => $this->id]);
        return ($query->fetch())['number'];
    }

    public function get_subscriptors(): array
    {
        $query = self::execute(
            "SELECT DISTINCT users.* FROM users, subscriptions WHERE subscriptions.user = users.id AND tricount=:id AND subscriptions.user != :user_id ORDER BY users.full_name",
            ["id" => $this->id, "user_id" => $this->creator->id]
        );
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $user) {
            $array[] = new User($user['mail'], $user['hashed_password'], $user['full_name'], $user['role'], $user['iban'], $user['id']);
        }
        return $array;
    }

    public function get_subscriptors_with_creator(): array
    {
        $query = self::execute(
            "SELECT DISTINCT users.* FROM users, subscriptions WHERE subscriptions.user = users.id AND subscriptions.tricount= :id ORDER BY users.full_name",
            ['id' => $this->id]
        );
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $user) {
            $array[] = new User($user['mail'], $user['hashed_password'], $user['full_name'], $user['role'], $user['iban'], $user['id']);
        }
        return $array;
    }

    public function get_cbo_users(): array //retourne les user pouvant être ajoutés à un tricount
    {
        $query = self::execute(
            "SELECT * FROM users WHERE id != :creator_id AND id NOT IN (SELECT user FROM subscriptions WHERE tricount = :tricount_id) ORDER BY full_name ASC",
            ["creator_id" => $this->creator->id, "tricount_id" => $this->id]
        );
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $user) {
            $array[] = new User($user["mail"], $user["hashed_password"], $user["full_name"], $user["role"], $user["iban"], $user["id"]);
        }
        return $array;
    }

    public function get_deletables(): array
    {
        $subs = $this->get_subscriptors();
        $array = [];
        foreach ($subs as $sub) {
            if ($this->get_balance($sub->id) == 0)
                $array[] = $sub;
        }

        return $array;
    }

    public function get_subs_as_json(): string
    {
        $subs = $this->get_subscriptors_with_creator();
        $table = [];
        foreach ($subs as $sub) {
            $row = [];
            $row["id"] = $sub->id;
            $row["name"] = $sub->full_name;
            $row["deletable"] = in_array($sub, $this->get_deletables());
            $table[$sub->id] = $row;
        }
        return json_encode($table);
    }

    public function get_addables_as_json(): string
    {
        $subs = $this->get_cbo_users();
        $table = [];
        foreach ($subs as $sub) {
            $row = [];
            $row["id"] = $sub->id;
            $row["name"] = $sub->full_name;
            $row["deletable"] = in_array($sub, $this->get_deletables());
            $table[$sub->id] = $row;
        }
        return json_encode($table);
    }

// --------------------------- Get Operation du tricount ------------------------------------ 


    public function get_operations(): array
    {
        $query = self::execute("SELECT * FROM operations WHERE tricount = :id ORDER BY operation_date DESC", ["id" => $this->id]);
        $data = $query->fetchAll();
        $array = [];
        foreach ($data as $op) {
            $array[] = new Operation($op['title'], Tricount::get_tricount_by_id($op['tricount']), $op['amount'], $op['operation_date'], User::get_user_by_id($op['initiator']), $op['created_at'], $op['id']);
        }
        return $array;
    }

    public function get_operation_as_json(): string
    {
        $operations = $this->get_operations();
        $table = [];
        foreach ($operations as $operation) {
            $row = [];
            $row ["id"] = $operation->id;
            $row ["title"] = $operation->title;
            $row ["amount"] = $operation->amount;
            $row ["operation_date"] = $operation->operation_date;
            $row ["created_at"] = $operation->created_at;
            $row ["initiator"] = $operation->initiator->full_name;
            $table[] = $row;  
        }
        return json_encode($table);
    }


// --------------------------- Persist // Delete Subs ------------------------------------ 


    public function persist_subscriptor(int $id): void
    {
        self::execute("INSERT INTO subscriptions(user, tricount) VALUES(:user, :tricount)", ["user" => $id, 'tricount' => $this->id]);
    }

    public function delete_subscriptor(int $user_id, int $tricount_id): void
    {
        $this->delete_repartition_template_item($user_id, $tricount_id);
        self::execute("DELETE FROM subscriptions WHERE user=:user_id AND tricount=:tricount_id ", ["user_id" => $user_id, "tricount_id" => $this->id]);
    }

    private function delete_repartition_template_item(int $user_id, int $tricount_id): void
    {
        $repartition_templates = RepartitionTemplates::get_all_repartition_templates_by_tricount_id($tricount_id);
        foreach($repartition_templates as $template) {
            self::execute("DELETE FROM repartition_template_items WHERE user = :id AND repartition_template = :template", ["id" => $user_id, "template" => $template->id]);
        }
    }


// --------------------------- Validate && Persist // Private Delete && delete Cascade des Tricounts ------------------------------------ 


    public function validate(): array
    {
        $errors = [];
        if (!strlen($this->title) > 0) {
            $errors['required'] = "Title is required.";
        }
        if (strlen($this->title) < 3 || strlen($this->title) > 256) {
            $errors['title_length'] = "Title length must be between 3 and 256.";
        }
        if (strlen($this->description) > 0 && (!(strlen($this->description) >= 3) || strlen($this->description) > 1024)) {
            $errors['description_length'] = "Description length must be between 3 and 1024.";
        }
        $array = self::get_tricounts_list($this->creator->id);
        foreach ($array as $data) {
            if (strtoupper($this->title) == strtoupper($data->title) && $this->id != $data->id) {
                $errors['unique_title'] = "Title must be unique";
            }
        }
        return $errors;
    }

    public function persist_tricount(): Tricount
    {
        if ($this->id != 0) {
            self::execute(
                "UPDATE tricounts SET title =:title, description =:description WHERE id=:id",
                ["title" => $this->title, "description" => $this->description, "id" => $this->id]
            );
        } else {
            self::execute(
                "INSERT INTO tricounts(title, description, created_at, creator) VALUES(:title, :description, :created_at, :creator)",
                ["title" => $this->title, "description" => $this->description, "created_at" => $this->created_at, "creator" => $this->creator->id]
            );
            $this->id = Model::lastInsertId();
            self::execute("INSERT INTO subscriptions(user, tricount) VALUES (:user, :tricount)", ["user" => $this->creator->id, "tricount" => $this->id]);
        }
        return $this;
    }

    public function delete_tricount_cascade(): void
    {
        $this->delete_repartition_item();
        $this->delete_repartition();
        $this->delete_template();
        $this->delete_operation();
        $this->delete_subscriptors();
        $this->delete_tricount();
    }

    private function delete_tricount(): void
    {
        self::execute("DELETE FROM tricounts WHERE id= :id ", ["id" => $this->id]);
    }

    private function delete_operation(): void
    {
        self::execute("DELETE FROM operations WHERE tricount= :tricount_id ", ["tricount_id" => $this->id]);
    }

    private function delete_template(): void
    {
        self::execute("DELETE FROM repartition_templates WHERE tricount= :tricount_id ", ["tricount_id" => $this->id]);
    }

    private function delete_repartition(): void
    {
        self::execute("DELETE FROM repartitions WHERE operation IN (SELECT id FROM operations WHERE tricount= :id)", ["id" => $this->id]);
    }

    private function delete_subscriptors(): void
    {
        self::execute("DELETE FROM subscriptions WHERE tricount= :tricount_id", ["tricount_id" => $this->id]);
    }

    private function delete_repartition_item(): void
    {
        self::execute("DELETE FROM repartition_template_items WHERE repartition_template IN (SELECT id FROM repartition_templates WHERE tricount = :tricount_id)", ["tricount_id" => $this->id]);
    }
}
