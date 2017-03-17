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
    const RESULT_INFO_FIND_FAILED = 8;

    const DEFAULT_DB_QUERY_PAGE = -1;
    const DEFAULT_DB_QUERY_NUM_IN_PAGE = -1;

    const CREATE = 11;
    const READ = 12;
    const UPDATE = 13;
    const DELETE = 14;


    private $DATABASE_NAME;

    private $connection;
    private $result;

    private $reflect;

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
        $this->reflect = new ReflectionClass($clz);
        $properties = $this->reflect->getProperties();

        $tableNameInSQL = $clz;

        $sql_pre = "INSERT INTO ".strtolower($tableNameInSQL)." (";
        $sql_post = ") VALUES ( ";

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyValue = $entity->$propertyName;

            $sql_pre = $sql_pre.$propertyName.",";
            $sql_post = $sql_post."'".$propertyValue."',";
        }

        $sql_pre = substr($sql_pre,0,-1);
        $sql_post = substr($sql_post,0,-1).")";
        $sql = $sql_pre.$sql_post;

        if ($this->connection->query($sql) === TRUE)
            return self::RESULT_INFO_INSERT_SUCCESS;
        else
            return self::RESULT_INFO_INSERT_FAILED;
    }

    public function update($entity)
    {
        $clz = get_class($entity);
        $this->reflect = new ReflectionClass($clz);
        $properties = $this->reflect->getProperties();

        $tableNameInSQL = $clz;

        $sql_pre = "UPDATE ".strtolower($tableNameInSQL)." SET ";
        $sql_post = "WHERE id = ";

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyValue = $entity->$propertyName;

            if ($propertyValue == null) continue;

            if ($propertyName == "id") $sql_post = $sql_post."'".$propertyValue."'";
            else $sql_pre = $sql_pre.$propertyName." = '".$propertyValue."' ,";
        }

        $sql_pre = substr($sql_pre,0,-1);
        $sql = $sql_pre.$sql_post;

        if ($this->connection->query($sql) === TRUE)
            return self::RESULT_INFO_UPDATE_SUCCESS;
        else
            return self::RESULT_INFO_UPDATE_FAILED;
    }

    public function delete($entity)
    {
        $clz = get_class($entity);
        $this->reflect = new ReflectionClass($clz);
        $properties = $this->reflect->getProperties();

        $tableNameInSQL = $clz;

        $sql = "DELETE FROM ".strtolower($tableNameInSQL)." WHERE id = ";

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyValue = $entity->$propertyName;

            if ($propertyName == "id") $sql = $sql."'".$propertyValue."'";
        }

        if ($this->connection->query($sql) === TRUE)
            return self::RESULT_INFO_DELETE_SUCCESS;
        else
            return self::RESULT_INFO_DELETE_FAILED;
    }

    public function find($entity,$page = self::DEFAULT_DB_QUERY_PAGE,$num = self::DEFAULT_DB_QUERY_NUM_IN_PAGE)
    {
        $clz = get_class($entity);
        $this->reflect = new ReflectionClass($clz);
        $properties = $this->reflect->getProperties();

        $tableNameInSQL = $clz;

        $sql_pre = "SELECT ";
        $sql_post = " FROM ".strtolower($tableNameInSQL)." WHERE 1 = 1";

        foreach ($properties as $property)
        {
            $propertyName = $property->getName();
            $propertyValue = $entity->$propertyName;

            $sql_pre = $sql_pre.$propertyName.", ";

            if ($propertyValue == null) continue;

            $sql_post = $sql_post." AND ".$propertyName." = '".$propertyValue."'";
        }

        $sql_pre = substr($sql_pre,0,-2);
        $sql = $sql_pre.$sql_post;

        if ($page != self::DEFAULT_DB_QUERY_NUM_IN_PAGE || $num != self::DEFAULT_DB_QUERY_NUM_IN_PAGE)
        {
            $sql = $sql." limit ".($page-1)*$num.",".$num;
        }

        $this->result = $this->connection->query($sql);

        if (!$this->result)
            return self::RESULT_INFO_FIND_FAILED;
        else
        {
            $fetchResults = $this->result->fetch_all(MYSQLI_ASSOC);
            $result = array();
            foreach ($fetchResults as $fetchResult)
            {
                $id = null;
                $entity = $this->reflect->newInstance();
                foreach ($properties as $property)
                {
                    $propertyName = $property->getName();
                    if ($propertyName == "id") $id = $fetchResult["$propertyName"];
                    $entity->$propertyName = $fetchResult["$propertyName"];
                }
                $result[$id] = $entity;
            }
            return $this->result;
        }

    }

    public function nativeSql($sql,$type,$entity)
    {
        switch ($type)
        {
            case 11:

                break;

            case 12:

                break;

            case 13:

                break;

            case 14:

                break;

            default:

                break;
        }
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

