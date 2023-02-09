<?php
require_once 'ConnectDB.php';
require_once 'AllMessage.php';
require_once 'ValidationUtils.php';

if (!file_exists('Human.php')) {
    die (AllMessage::ERROR_LOAD_FILE);
} else {
    require_once 'Human.php';
}

class ListOfPeople
{
    private array $arrayOfId;

    public function __construct($id, $symbol)
    {
        if (!ValidationUtils::validID($id)) {
            throw new Exception(AllMessage::ERROR_ID);
        }
        if (!ValidationUtils::isSymbolValid($symbol)) {
            throw new Exception(AllMessage::ERROR_SYMBOLS);
        }
        $query = "SELECT `id` FROM `people` WHERE `id`" . $symbol . "?";
        $connection = ConnectDB::getInstance()->getConnect();
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->arrayOfId = $result->fetch_all();
    }

    public function getArrayOfId()
    {
        return $this->arrayOfId;
    }

    public function deleteWithArray()
    {
        foreach ($this->getArrayOfId() as $array) {
            foreach ($array as $val) {
                Human::fromId(intval($val))->delete();
            }
        }
    }

    public function getArrayOfHuman()
    {
        $array = [];
        foreach ($this->getArrayOfId() as $value) {
            foreach ($value as $val) {
                $array[] = Human::fromId(intval($val));
            }
        }
        return $array;
    }

}

//$a = new ListOfPeople(19, '<=');
//print_r($a->deleteWithArray());
//print_r($a->getArrayOfHuman());