<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * @author ofanebob
 * @copyright 2014
 * Perubahan method DB_Class dari pemanggilan database standar mysql
 * Dirubah menjadi method OOP mysqli
 * Berlaku untuk semua versi PHP kecuali di atas 5.4.9 Depreceted
 */
class DB_Class {
    protected $_connecting;
    protected $_hosting;
    protected $_username;
    protected $_password;
    protected $_database;

    function __construct(){
        $this->_hosting = DB_HOST;
        $this->_username = DB_USERNAME;
        $this->_password = DB_PASSWORD;
        $this->_database = DB_NAME;
    }
 
    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        try
        {
            $this->_connecting = new mysqli($this->_hosting, $this->_username, $this->_password, $this->_database);
            return $this->_connecting;
        }
        catch(Exception $e)
        {
            throw new DBCException($e->getMessage());
        }
    }
}

/**
 * DBCException extends
 */
class DBCException extends Exception{}
?>