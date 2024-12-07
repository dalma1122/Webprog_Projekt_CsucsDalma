<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'online_dispensary';
    private $username = 'root';
    private $password = '';
    public $conn;

    // Ez a metódus biztosítja a kapcsolatot az adatbázissal, és visszaadja a PDO objektumot, amely lehetővé teszi az adatbázis műveletek végrehajtását.
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8"); // UTF-8 karakterkódolás, hogy a speciális karaktereket (pl. ékezetes betűk) helyesen kezelje.
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
