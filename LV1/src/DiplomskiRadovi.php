<?php
include_once 'iRadovi.php';
class DiplomskiRadovi implements iRadovi
{
    private $id = NULL;
    private $naziv_rada = NULL;
    private $tekst_rada = NULL;
    private $link_rada = NULL;
    private $oib_tvrtke = NULL;

    private static $conn = NULL;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->create($data);
        }

        if (self::$conn === null) {
            $this->initDatabase();
        }
    }

    private function initDatabase()
    {
        try {
            $host = '127.0.0.1';
            $dbname = 'radovi';
            $username = 'radovi_user';
            $password = 'ChangeThisPassword123!';

            self::$conn = new PDO(
                "mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

        } catch (PDOException $e) {
            die("Error: Can't connect to database: " . $e->getMessage());
        }
    }
    public function get_id()
    {
        return $this->id;
    }
    public function get_naziv_rada()
    {
        return $this->naziv_rada;
    }
    public function get_tekst_rada()
    {
        return $this->tekst_rada;
    }
    public function get_link_rada()
    {
        return $this->link_rada;
    }
    public function get_oib_tvrtke()
    {
        return $this->oib_tvrtke;
    }
    #[Override]
    public function create($data)
    {
        $this->naziv_rada = $data['naziv_rada'] ?? '';
        $this->tekst_rada = $data['tekst_rada'] ?? '';
        $this->link_rada = $data['link_rada'] ?? '';
        $this->oib_tvrtke = $data['oib_tvrtke'] ?? '';
    }

    #[Override]
    public function save()
    {
        try {
            $selectDiplomskiQuery = self::$conn->prepare("SELECT id FROM diplomski_radovi WHERE link_rada = ?");
            $selectDiplomskiQuery->execute([$this->link_rada]);

            if ($selectDiplomskiQuery->fetch()) {
                return false;
            }

            $query = "INSERT INTO diplomski_radovi (naziv_rada, tekst_rada, link_rada, oib_tvrtke)
                    VALUES (?, ?, ?, ?)";

            $selectDiplomskiQuery = self::$conn->prepare($query);
            $selectDiplomskiQuery->execute([
                $this->naziv_rada,
                $this->tekst_rada,
                $this->link_rada,
                $this->oib_tvrtke
            ]);

            $this->id = self::$conn->lastInsertId();
            return true;
        } catch (PDOException $e) {
            echo "Error while writing: " . $e->getMessage() . "</br>";
            return false;
        }
    }

    #[Override]
    public function read()
    {
        if ($this->id !== null) {
            return [
                'naziv_rada' => $this->naziv_rada,
                'tekst_rada' => $this->tekst_rada,
                'link_rada' => $this->link_rada,
                'oib_tvrtke' => $this->oib_tvrtke
            ];
        }
        return self::readAll();
    }

    public static function readAll()
    {
        try {
            if (self::$conn === null) {
                $temp = new DiplomskiRadovi();
            }

            $selectDiplomskiQuery = self::$conn->query("SELECT * FROM diplomski_radovi ORDER BY id DESC");
            $radovi = [];

            while ($row = $selectDiplomskiQuery->fetch(PDO::FETCH_ASSOC)) {
                $rad = new DiplomskiRadovi($row);
                $radovi[] = $rad;
            }

            return $radovi;
        } catch (PDOException $e) {
            echo "Error while reading: " . $e->getMessage() . "\n";
            return [];
        }
    }
    public static function readById($id)
    {
        try {
            if (self::$conn === null) {
                $temp = new DiplomskiRadovi();
            }

            $selectDiplomskiQuery = self::$conn->prepare("SELECT * FROM diplomski_radovi WHERE id = ?");
            $selectDiplomskiQuery->execute([$id]);

            $row = $selectDiplomskiQuery->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new DiplomskiRadovi($row);
            }
            return null;
        } catch (PDOException $e) {
            echo "Error while reading: " . $e->getMessage() . "</br";
            return null;
        }
    }
}
