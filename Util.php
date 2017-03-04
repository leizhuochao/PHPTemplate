<?php

/**
 * Created by PhpStorm.
 * User: leizhuochao
 * Date: 2017/3/3
 * Time: 下午2:07
 */
class Database
{

    const URL = "127.0.0.1";
    const USERNAME = "root";
    const PASSWORD = "941203";
    const DEFAULT_DATABASE_NAME = "Travel";

    const RESULT_INFO_INSERT_SUCCESS = 1;
    const RESULT_INFO_INSERT_FAILED = 2;
    const RESULT_INFO_UPDATE_SUCCESS = 3;
    const RESULT_INFO_UPDATE_FAILED = 4;
    const RESULT_INFO_DELETE_SUCCESS = 5;
    const RESULT_INFO_DELETE_FAILED = 6;

    private $DATABASE_NAME;

    private $connection;
    private $result;

    private $reflct;

    function __construct($db_name=self::DEFAULT_DATABASE_NAME)
    {    // 构造函数
        $this->connection = new mysqli(self::URL,self::USERNAME,self::PASSWORD);
        if (!$this->connection) echo "Connect to database failed";
        $this->DATABASE_NAME = trim($db_name);
        $this->connection->select_db($this->DATABASE_NAME);
        $this->connection->set_charset("utf8");
    }

    public function insert($entity)
    {
        $clz = get_class($entity);
        $this->reflct = new ReflectionClass($clz);
        $properties = $this->reflct->getProperties();

        $tableNameInSQL = $clz;

        $sql_pre = "INSERT INTO ".strtolower($tableNameInSQL)." (";
        $sql_post = ") VALUES ( ";

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyValue = $entity->$propertyName;

            $sql_pre = $sql_pre.$propertyName.",";
            $sql_post = $sql_post.$propertyValue.",";
        }

        $sql_pre = substr($sql_pre,0,-1);
        $sql_post = substr($sql_post,0,-1).")";
        $sql = $sql_pre.$sql_post;

        $sql = "INSERT INTO travel.province (id,name,info) VALUE (1,'测试省份','测试信息')";

        if ($this->connection->query($sql) === TRUE)
            return self::RESULT_INFO_INSERT_SUCCESS;
        else
            return self::RESULT_INFO_INSERT_FAILED;
    }

    public function update($entity)
    {

    }

    public function delete($entity)
    {

    }

    public function find($entity)
    {

    }


    public function setDatabaseName($databaseName)
    {
        $this->DATABASE_NAME = $databaseName;
        $this->connection->select_db($this->DATABASE_NAME);
    }

}

class File
{
    private $INPUT_FILE_PATH;
    private $OUTPUT_FILE_PATH;

    function __construct()
    {

    }
}

class FileToDatabase
{

}

class InterfaceToFile
{

}

