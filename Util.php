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

    const TYPE_NULL = 10;
    const CREATE = 11;
    const READ = 12;
    const UPDATE = 13;
    const DELETE = 14;
    const TYPE_OTHER = 15;


    private $DATABASE_NAME = null;

    private $connection = null;
    private $results = null;
    private $return_info = null;

    private $reflect = null;

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
            $this->return_info = self::RESULT_INFO_INSERT_SUCCESS;
        else
            $this->return_info = self::RESULT_INFO_INSERT_FAILED;
        return $this->return_info;
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
            $this->return_info = self::RESULT_INFO_UPDATE_SUCCESS;
        else
            $this->return_info = self::RESULT_INFO_UPDATE_FAILED;
        return $this->return_info;
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
            $this->return_info = self::RESULT_INFO_DELETE_SUCCESS;
        else
            $this->return_info = self::RESULT_INFO_DELETE_FAILED;
        return $this->return_info;
    }

    public function find($entity,$query_page = self::DEFAULT_DB_QUERY_PAGE,$query_num = self::DEFAULT_DB_QUERY_NUM_IN_PAGE)
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

        if ($query_page != self::DEFAULT_DB_QUERY_NUM_IN_PAGE || $query_num != self::DEFAULT_DB_QUERY_NUM_IN_PAGE)
        {
            $sql = $sql." limit ".($query_page-1) * $query_num.",".$query_num;
        }

        $this->results = $this->connection->query($sql);

        if (!$this->results)
            return self::RESULT_INFO_FIND_FAILED;
        else
        {
            $fetchResults = $this->results->fetch_all(MYSQLI_ASSOC);
            $results = array();
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
                $results[$id] = $entity;
            }
            return $this->results;
        }
    }

    public function nativeSql($sql,$entity = NULL,$type_in_native_sql = self::TYPE_NULL)
    {
        $sql = trim($sql);
        $sql_type = strtolower(explode(" ",$sql)[0]);

        if ($type_in_native_sql == self::TYPE_NULL){
            switch ($sql_type)
            {
                case "create": $type_in_native_sql = self::CREATE; break;
                case "select": $type_in_native_sql = self::READ; break;
                case "update": $type_in_native_sql = self::UPDATE; break;
                case "delete": $type_in_native_sql = self::DELETE; break;
                default: $type_in_native_sql = self::TYPE_OTHER; break;
            }
        }

        switch ($type_in_native_sql)
        {
            case self::CREATE:
                if ($this->connection->query($sql) === TRUE)
                    $this->return_info = self::RESULT_INFO_INSERT_SUCCESS;
                else
                    $this->return_info = self::RESULT_INFO_INSERT_FAILED;
                break;

            case self::READ:

                break;

            case self::UPDATE:
                if ($this->connection->query($sql) === TRUE)
                    $this->return_info = self::RESULT_INFO_UPDATE_SUCCESS;
                else
                    $this->return_info = self::RESULT_INFO_UPDATE_FAILED;
                break;

            case self::DELETE:
                if ($this->connection->query($sql) === TRUE)
                    $this->return_info = self::RESULT_INFO_DELETE_SUCCESS;
                else
                    $this->return_info = self::RESULT_INFO_DELETE_FAILED;
                break;

            case self::TYPE_OTHER:

                break;
            default:

                break;
        }
        return $this->return_info;
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

