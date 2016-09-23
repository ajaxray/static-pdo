<?php

namespace StaticPdo;

/**
 * Db
 *
 * A simple wrapper for PDO.
 * Inspired by the sweet PDO wrapper from http://www.fractalizer.ru
 *
 * @author  Anis uddin Ahmad <anisniit@gmail.com>
 * @link    http://www.fractalizer.ru/frpost_120/php-pdo-wrapping-and-making-sweet/
 * @link    http://ajaxray.com
 */
class Db
{
    private static $_pdoObject = null;

    protected static $_fetchMode = \PDO::FETCH_ASSOC;
    protected static $_connectionStr = null;
    protected static $_driverOptions = array();

    private static $_username = null;
    private static $_password = null;

    /**
     * Set connection information
     *
     * @example    Db::setConnectionInfo('basecamp','dbuser', 'password', 'mysql', 'http://mysql.abcd.com');
     */
    public static function setConnectionInfo($schema, $username = null, $password = null, $database = 'mysql', $hostname = 'localhost')
    {
        if($database == 'mysql') {
            self::$_connectionStr = "mysql:dbname=$schema;host=$hostname";
            self::$_username      = $username;
            self::$_password      = $password;
        } else if($database == 'sqlite'){
            // For sqlite, $schema is the file path
            self::$_connectionStr = "sqlite:$schema";
        }

        // Making the connection blank
        // Will connect with provided info on next query execution
        self::$_pdoObject = null;
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
    public static function execute($sql, $params = array())
    {
        $statement = self::_query($sql, $params);
        return $statement->rowCount();
    }

    /**
     * Execute a statement and returns a single value
     *
     * @param   string  $sql    SQL statement
     * @param   array   $params A single value or an array of values
     * @return  mixed
     */
    public static function getValue($sql, $params = array())
    {
        $statement = self::_query($sql, $params);
        return $statement->fetchColumn(0);
    }

    /**
     * Execute a statement and returns the first row
     *
     * @param   string  $sql    SQL statement
     * @param   array   $params A single value or an array of values
     * @return  array   A result row
     */
    public static function getRow($sql, $params = array())
    {
        $statement = self::_query($sql, $params);
        return $statement->fetch(self::$_fetchMode);
    }

    /**
     * Execute a statement and returns row(s) as 2D array
     *
     * @param   string  $sql    SQL statement
     * @param   array   $params A single value or an array of values
     * @return  array   Result rows
     */
    public static function getResult($sql, $params = array())
    {
        $statement = self::_query($sql, $params);
        return $statement->fetchAll(self::$_fetchMode);
    }

    public static function getLastInsertId($sequenceName = "")
    {
        return self::$_pdoObject->lastInsertId($sequenceName);
    }

    public static function setFetchMode($fetchMode)
    {
        self::_connect();
        self::$_fetchMode = $fetchMode;
    }

    public static function getPDOObject()
    {
        self::_connect();
        return self::$_pdoObject;
    }

    public static function beginTransaction()
    {
        self::_connect();
        self::$_pdoObject->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$_pdoObject->commit();
    }

    public static function rollbackTransaction()
    {
        self::$_pdoObject->rollBack();
    }

    public static function setDriverOptions(array $options)
    {
        self::$_driverOptions = $options;
    }

    private static function _connect()
    {
        if(self::$_pdoObject != null){
            return;
        }
        
        if(self::$_connectionStr == null) {
            throw new \PDOException('Connection information is empty. Use Db::setConnectionInfo to set them.');
        }

        self::$_pdoObject = new \PDO(self::$_connectionStr, self::$_username, self::$_password, self::$_driverOptions);
    }

    /**
     * Prepare and returns a PDOStatement
     *
     * @param   string  $sql SQL statement
     * @param   array   $params Parameters. A single value or an array of values
     * @return  PDOStatement
     */
    private static function _query($sql, $params = array())
    {
        if(self::$_pdoObject == null) {
            self::_connect();
        }

        $statement = self::$_pdoObject->prepare($sql, self::$_driverOptions);

        if (! $statement) {
            $errorInfo = self::$_pdoObject->errorInfo();
            throw new \PDOException("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is $errorInfo[1]");
        }

        $paramsConverted = (is_array($params) ? ($params) : (array ($params )));

        if ((! $statement->execute($paramsConverted)) || ($statement->errorCode() != '00000')) {
            $errorInfo = $statement->errorInfo();
            throw new \PDOException("Database error [{$errorInfo[0]}]: {$errorInfo[2]}, driver error code is $errorInfo[1]");
        }

        return $statement;
    }
}
