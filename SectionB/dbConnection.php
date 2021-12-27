<?php

class dbConnection {
  private $db_host = "localhost";
  private $db_user = "root";
  private $db_pass = "mysql";
  private $db_name = "db_teste";

  public function connect() {
    $mysql_connect_str = "mysql:host=$this->db_host;dbname=$this->db_name";
    $dbConnection = new PDO($mysql_connect_str, $this->db_user, $this->db_pass);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
  }

  public function close() {
    $dbConnection = null;
  }

  public function ChallengeOne() {
    $dbConnection = $this->connect();
    $query = "SELECT CONCAT(first_name, ' ', last_name) as Name, DEPARTMENT_ID as Department_ID 
    FROM employees WHERE DEPARTMENT_ID = 30 OR DEPARTMENT_ID = 100 ORDER BY DEPARTMENT_ID ASC";
    $stmt = $dbConnection->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $this->close();
    return $result;
  }


  public function ChallengeTwo() {
    $dbConnection = $this->connect();
    $query = "SELECT MANAGER_ID, SALARY FROM employees ORDER BY SALARY ASC LIMIT 1";
    $stmt = $dbConnection->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    $this->close();
    return $result;
  }

  public function ChallengeThree($lastName) {
    $dbConnection = $this->connect();
    $query = "SELECT CONCAT(first_name, ' ', last_name) as Name, SALARY 
      FROM employees WHERE SALARY > (SELECT SALARY FROM employees WHERE last_name =:lastName) 
      ORDER BY SALARY DESC";
    $stmt = $dbConnection->prepare($query);
    $stmt->execute(['lastName' => $lastName]);
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $this->close();
    return $result;
  }

  public function ChallengeFour($city) {
    $query = "SELECT CONCAT(first_name, ' ', last_name) as Name, JOB_TITLE as Job, DEPARTMENT_ID 
      FROM employees INNER JOIN jobs ON employees.JOB_ID = jobs.JOB_ID 
      WHERE employees.DEPARTMENT_ID IN (SELECT DEPARTMENT_ID 
      FROM departments 
      WHERE LOCATION_ID = (SELECT LOCATION_ID FROM locations WHERE CITY =:city))";
    $dbConnection = $this->connect();
    $stmt = $dbConnection->prepare($query);
    $stmt->execute(['city' => $city]);
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    $this->close();
    return $result;
  }


  public function ChallengeFive() {
    $query = "SELECT COUNT(*) as Total_Employees, d.DEPARTMENT_NAME as Department_Name
      FROM db_teste.employees AS e
      INNER JOIN db_teste.departments as d ON d.DEPARTMENT_ID = e.DEPARTMENT_ID
      GROUP BY d.DEPARTMENT_NAME ORDER BY Total_Employees DESC;";
    $dbConnection = $this->connect();
    $stmt = $dbConnection->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $this->close();
    return $result;
  }


  public function ExportObjectToJson($json, $fileName) {
    $fp = fopen($fileName, 'w');
    fwrite($fp, json_encode($json));
    fclose($fp);
  }

  public function arrayToXml($array, $fileName) {
    try {

      $xml = new SimpleXMLElement('<root/>');
      array_walk_recursive($array, function ($value, $key) use ($xml) {
        $xml->addChild($key, $value);
      });

      $dom = new DOMDocument('1.0');
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;
      $dom->loadXML($xml->asXML());
      $dom->save($fileName);
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function createDepartment($department_id, $department_name, $manager_id, $location_id) {
    try {
      $dbConnection = $this->connect();
      $query = "INSERT INTO departments (DEPARTMENT_ID, DEPARTMENT_NAME, MANAGER_ID, LOCATION_ID)
                VALUES (:department_id, :department_name, :manager_id, :location_id)";
      $stmt = $dbConnection->prepare($query);
      $stmt->execute(['department_name' => $department_name, 'manager_id' => $manager_id, 'location_id' => $location_id, 'department_id' => $department_id]);
      $this->close();
      return true;
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }


  public function removeDepartment($department_name) {
    try {
      $dbConnection = $this->connect();
      $query = "DELETE FROM departments WHERE DEPARTMENT_NAME = :department_name";
      $stmt = $dbConnection->prepare($query);
      $stmt->execute(['department_name' => $department_name]);
      $this->close();
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
}




$db = new dbConnection();


$chlg1 = $db->ChallengeOne();

$chlg2 = $db->ChallengeTwo();
$chlg3 = $db->ChallengeThree('Bell');
$chlg4 = $db->ChallengeFour('London');
$chlg5 = $db->ChallengeFive();


$db->createDepartment(280, 'Vendedor', 205, 1700);
