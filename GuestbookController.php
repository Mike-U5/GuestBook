<?php
class GuestbookController {
    private PDO $pdo;

    public function __construct($host, $user, $pass) {
        $this->pdo = new PDO("mysql:host=$host;dbname=local_db", $user, $pass);
    }

    public function get(): void {
        $query = $this->pdo->query("SELECT name, email, comment, created_at FROM guestbook ORDER BY created_at DESC;");
        $result = $query->fetchAll();
        echo json_encode($result, JSON_THROW_ON_ERROR);
    }

    public function post(): void {
        $validationErrors = array_filter([
            "name" => $this->validate('name', ["required" => true, "maxLength" => 255]),
            "comment" => $this->validate('comment', ["required" => true, "maxLength" => 1000]),
            "email" => $this->validate('email', ["required" => true, "isEmail" => true, "maxLength" => 255])
        ]);

        if (empty($validationErrors)) {
            $query = $this->pdo->prepare("INSERT INTO guestbook (name, email, comment) VALUES (:name, :email, :comment);");

            $name = htmlspecialchars($_POST['name']);
            $comment = htmlspecialchars($_POST['comment']);
            $email = htmlspecialchars($_POST['email']);

            $query->execute(['name' => $name, 'comment' => $comment, 'email' => $email]);
            echo json_encode(["success" => true], JSON_THROW_ON_ERROR);
        } else {
            echo json_encode(["success" => false, "errors" => $validationErrors], JSON_THROW_ON_ERROR);
        }
    }

    private function validate(string $index, array $rules): ?string {
        if (array_key_exists("required", $rules) && empty($_POST[$index])) {
            return "Dit veld is verplicht.";
        }
        if (array_key_exists("maxLength", $rules) && strlen($_POST[$index]) > $rules["maxLength"]) {
            return "Dit veld mag maximaal ". $rules["maxLength"] ." karakters bevatten.";
        }
        if (array_key_exists("isEmail", $rules) && !filter_var($_POST[$index], FILTER_VALIDATE_EMAIL)) {
            return "Het ingevulde e-mailadres is niet correct.";
        }

        return null;
    }
}
?>
