<?php
require_once "ConnectDB.php";
require_once "AllMessage.php";
require_once "ValidationUtils.php";
require_once "Config.php";

class Human
{
    private $id;
    private $name;
    private $surname;
    private $dateOfBirth;
    private $gender;
    private $placeOfBirth;

    private function __construct($id)
    {
        $this->id = $id;
    }

    public static function fromId($id)
    {
        if (!ValidationUtils::validID($id)) {
            throw new Exception(AllMessage::ERROR_ID);
        }
        $query = "SELECT `name`, `surname`, `date_of_birth`, 
            `gender`, `place_of_birth` FROM `people` WHERE id=?";
        $connection = ConnectDB::getInstance()->getConnect();
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if (!is_null($data)) {
            $human = new self($id);
            $human->initFields($data);
            return $human;
        }

        return null;
    }

    public static function fromData($data)
    {
        $config = Config::getConfig();
        $errors = [];
        if (!ValidationUtils::isFieldValid($data['name'], $config['length'])) {
            $errors[] = AllMessage::ERROR_NAME;
        }
        if (!ValidationUtils::isFieldValid($data['surname'], $config['length'])) {
            $errors[] = AllMessage::ERROR_SURNAME;
        }
        if (!ValidationUtils::regExpDate($data['date_of_birth'])) {
            $errors[] = AllMessage::ERROR_DATE_OF_BIRTH;
        }
        if (!ValidationUtils::validGender($data['gender'])) {
            $errors[] = AllMessage::ERROR_GENDER;
        }
        if (!ValidationUtils::isFieldValid($data['place_of_birth'], $config['lengthCity'])) {
            $errors[] = AllMessage::ERROR_PLACE_OF_BIRTH;
        }

        if (!empty($errors)) {
            throw new Exception(implode("\n", $errors));
        }

        $connection = ConnectDB::getInstance()->getConnect();
        $query = "INSERT INTO `people` (`name`, `surname`, `date_of_birth`, `gender`, `place_of_birth`) 
            VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sssss", $data['name'], $data['surname'], $data['date_of_birth'], $data['gender'], $data['place_of_birth']);
        $stmt->execute();

        $human = new self($connection->insert_id);
        $human->initFields($data);
        return $human;
    }

    private function initFields($data)
    {
        $this->name = $data['name'];
        $this->surname = $data['surname'];
        $this->dateOfBirth = $data['date_of_birth'];
        $this->gender = $data['gender'];
        $this->placeOfBirth = $data['place_of_birth'];
    }

    private function update()
    {
        $query = "UPDATE `people` SET `name`='$this->name',`surname`='$this->surname',`date_of_birth`='$this->dateOfBirth',
            `gender`='$this->gender',`place_of_birth`='$this->placeOfBirth' WHERE `id`=?";
        $connection = ConnectDB::getInstance()->getConnect();
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM `people` WHERE `id`=? ";
        $connection = ConnectDB::getInstance()->getConnect();
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
    }

    public static function genderTransform($gender)
    {
        if (!ValidationUtils::validGender($gender)) {
            throw new Exception(AllMessage::ERROR_GENDER);
        }
        if ($gender == 1) {
            return AllMessage::GENDER_FEMALE;
        } else {
            return AllMessage::GENDER_MALE;
        }
    }

    public static function dateTransform($date)
    {
        $dateNow = new DateTime();
        $dateOfBirth = new DateTime($date);
        $interval = $dateNow->diff($dateOfBirth);
        return $interval->y;
    }

    public function format()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'date_of_birth' => $this->dateOfBirth,
            'years' => Human::dateTransform($this->dateOfBirth),
            'gender' => Human::genderTransform($this->gender),
            'place_of_birth' => $this->placeOfBirth
            ];
    }
}

//$x=Human::fromId(1);
//var_dump($x->format());
//var_dump($x->delete());
//$a = Human::fromData(['name' => 'dadadsdds1', 'surname' => 'ig', "date_of_birth" => "1996-11-23", "gender" => '0', 'place_of_birth' => 'Bereza']);
//var_dump(Human::dateTransform("1996-02-03"));
//var_dump($x);