<?php
/**
* Db
*
* A simple wrapper for PDO. Inspired by the sweet PDO wrapper from http://www.fractalizer.ru
*
* @author  Anis uddin Ahmad <anisniit@gmail.com>
* @link    http://www.fractalizer.ru/frpost_120/php-pdo-wrapping-and-making-sweet/
* @link    http://ajaxray.com
*/
class DBWrapper
{
    public  $_pdoObject = null;

    protected  $_fetchMode = PDO::FETCH_ASSOC;
    public  $_connectionStr = null;
    protected  $_driverOptions = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

    private  $_username = null;
    private  $_password = null;
    
    public $test = "test scope not defined";

    
    function __construct()
    {
        $this->_connectionStr = $this->_connectionStr = "mysql:dbname=".MYSQL_DATABASE.";host=".MYSQL_SERVERNAME;
        $this->_username = MYSQL_USERNAME;
        $this->_password = MYSQL_PASSWORD;            
    }
    
    
    /**
    * Set connection information
    *
    * @example    Db::setConnectionInfo('basecamp','dbuser', 'password', 'mysql', 'http://mysql.abcd.com');
    */
    public function setConnectionInfo($schema, $username = null, $password = null, $database = 'mysql', $hostname = MYSQL_SERVERNAME)
    {
        if($database == 'mysql') {
            $this->_connectionStr = "mysql:dbname=$schema;host=$hostname";
            $this->_username = $username;
            $this->_password = $password;
        } else if($database == 'sqlite'){
                // For sqlite, $schema is the file path
                $this->_connectionStr = "sqlite:$schema";
            }

            // Making the connection blank
            // Will connect with provided info on next query execution
            $this->_pdoObject = null;
    }

    /**
    * Execute a statement and returns number of effected rows
    *
    * Should be used for query which doesn't return resultset
    *
    * @param   string  $sql    SQL statement
    * @param   array   $params A single value or an array of values
    * @return  integer number of effected rows
    */
    public function execute($sql, $params = array())
    {
        $statement = $this->_query($sql, $params);
        return $statement->rowCount();
    }
    
    public function execute2($sql, $params = array())
    {
        $statement = $this->_query($sql, $params);
        
        //print   $sql;
        
        //fetch the return of the execute
        /*
        $results = array();
        $icounter = 0;
        
        do 
        {                 
             $results []= $statement->fetchAll();
             $icounter++;
             print ($icounter . "<br/>");
        } while ($statement->nextRowset());
        die();
        */
        return  $statement;
    }
    
    /**
    * return the sql phrase for insert and update
    * 
    */
    public function makeSQL($fieldName, $fieldValue, $valueType, $isReturnValueOnly = false, $suffixComma = ", ", $replaceValue = null)
    {
        $sql = "";
        if (!isset($fieldValue))
            $fieldValue = "";
        
        $valueType = strtolower($valueType);
        if ($valueType == "number")    
        {
            //replace null if not valid number
            if(!is_numeric($fieldValue))
                $fieldValue = $replaceValue;
                
        }
        elseif($valueType == "datetime")    
        {
            //replace null if not valid date time
        }
        elseif($valueType == "date")
        {
            //replace null if not valid date
        }
        else    //string
        {
            //replace null if not string
            if(!is_string($fieldValue))
                $fieldValue = $replaceValue;
        }
        
        if (!$isReturnValueOnly)
        {
            
            if (!is_null($fieldValue))
                $sql .= $fieldName . " = '" . $fieldValue . "'";
            else    
                $sql .= $fieldName . " = null";                
        }
        else
        {
            if (!is_null($fieldValue))
                $sql .= "'" . $fieldValue . "'";
            else    
                $sql .= "null"; 
        }
        
        $sql .= $suffixComma;       
        
        return $sql;
    }

    /**
    * Execute a statement and returns a single value
    *
    * @param   string  $sql    SQL statement
    * @param   array   $params A single value or an array of values
    * @return  mixed
    */
    public function getOneValue($sql, $params = array())
    {
        $statement = $this->_query($sql, $params); 
        return $statement->fetchColumn(0);
    }

    /**
    * Execute a statement and returns the first row
    *
    * @param   string  $sql    SQL statement
    * @param   array   $params A single value or an array of values
    * @return  array   A result row
    */
    public function getOneRow($sql, $params = array())
    {
        $statement = $this->_query($sql, $params);
        return $statement->fetch($this->_fetchMode);
    }

    /**
    * Execute a statement and returns row(s) as 2D array
    *
    * @param   string  $sql    SQL statement
    * @param   array   $params A single value or an array of values
    * @return  array   Result rows
    */
    public function getManyRow($sql, $params = array())
    {        
        $statement = $this->_query($sql, $params);                
        return $statement->fetchAll($this->_fetchMode);
        
    }

    public function getLastInsertId($sequenceName = "")
    {                    
        if ($this->_pdoObject == null) return false;
        return $this->_pdoObject->lastInsertId($sequenceName);
    }

    public function setFetchMode($fetchMode)
    {
        $this->_connect();
        $this->_fetchMode = $fetchMode;        
    }

    /**
    * Return PDO object
    * @return PDO
    * 
    */
    public function getPDOObject()
    {
        $this->_connect();
        return $this->_pdoObject;
    }

    public function beginTransaction()
    {
        $this->_connect();
        $this->_pdoObject->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->_pdoObject->commit();
    }

    public function rollbackTransaction()
    {
        $this->_pdoObject->rollBack();
    }

    private function _connect()
    {
        if($this->_pdoObject != null){
            return false;
        }

        if($this->_connectionStr == null) {
            return false;
            //throw new PDOException('Connection information is empty. Use Db::setConnectionInfo to set them.');
        }

        $this->_pdoObject = new PDO($this->_connectionStr, $this->_username, $this->_password, $this->_driverOptions);
    }

    /**
    * Prepare and returns a PDOStatement
    *
    * @param   string  $sql SQL statement
    * @param   array   $params Parameters. A single value or an array of values
    * @return  PDOStatement
    */
    private function _query($sql, $params = array())
    {        
        if($this->_pdoObject == null) {
            $this->_connect();
        }

        $statement = $this->_pdoObject->prepare($sql, $this->_driverOptions);        

        if (! $statement) {
            $errorInfo = $this->_pdoObject->errorInfo();
            log_error($this->interpolateQuery($sql, $params));
            throw new PDOException("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is $errorInfo[1]");
        }

        $paramsConverted = (is_array($params) ? ($params) : (array ($params )));

        if ((! $statement->execute($paramsConverted)) || ($statement->errorCode() != '00000')) {
            $errorInfo = $statement->errorInfo();
            log_error($this->interpolateQuery($sql, $params));
            throw new PDOException("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is $errorInfo[1]");
        }

        return $statement;
    }
    
    /**
    * Replaces any parameter placeholders in a query with the value of that
    * parameter. Useful for debugging. Assumes anonymous parameters from 
    * $params are are in the same order as specified in $query
    *
    * @param string $query The sql query with parameter placeholders
    * @param array $params The array of substitution parameters
    * @return string The interpolated query
    */
    public function interpolateQuery($query, $params) {
        $keys = array();

        $params = (is_array($params) ? ($params) : (array ($params )));                 
        
        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }
            //AnLT: wrap value by '', or put null
            if (is_null($value))
            {
                $value = 'null';
            }
            else
            {
                $value = "'".$value."'";
            }
            $params[$key] = $value;
        }

        $query = preg_replace($keys, $params, $query, 1, $count);

        #trigger_error('replaced '.$count.' keys');
        //AnLT 2011 12 26: add br to query for easy debugging
        return $query."<br/>";
    }
    
    public function setTestScope($input)
    {
        $this->test = "Test scope: " . $input;
    }
    
    public function printTestScope()
    {
        print "<br/>" . $this->test;
    }
}
