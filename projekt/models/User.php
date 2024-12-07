<?php
class User {
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ez a metódus a felhasználó regisztrálásáért felelős.
    public function register() {

        // Ellenőrzi, hogy az email cím már szerepel-e az adatbázisban, majd ha nem, akkor létrehozza az új felhasználót
        if ($this->emailExists()) {
            echo 'Ez az email cím már foglalt.';
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " SET name=:name, email=:email, password=:password, role=:role, created_at=NOW()";

        $stmt = $this->conn->prepare($query);

        // htmlspecialchars() és strip_tags() biztosítják, hogy az adatok biztonságosak legyenek.
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT); // A jelszót bcrypt-el titkosítja
        $this->role = htmlspecialchars(strip_tags($this->role));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Ez a metódus ellenőrzi, hogy egy adott email cím már létezik-e az adatbázisban:
    public function emailExists() {
        $query = "SELECT users_id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            return true;
        }
        return false;
    }

    // A metódus az email alapján keres egy felhasználót, majd ellenőrzi, hogy a megadott jelszó megegyezik-e az adatbázisban tárolt titkosított jelszóval.
    public function login() {
        $query = "SELECT users_id, name, email, password, role FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Ha a jelszavak egyeznek, bejelentkezik a felhasználó, és a felhasználó adatait visszaállítja.
            if(password_verify($this->password, $row['password'])) {
                $this->user_id = $row['users_id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->role = $row['role'];
                return true;
            }
        }
        return false;
    }
}
?>
