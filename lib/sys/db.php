<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * @author ofanebob
 * @copyright 2014
 * Perubahan method DB_Class dari pemanggilan database standar mysql
 * Dirubah menjadi method OOP mysqli
 * Berlaku untuk semua versi PHP kecuali di atas 5.4.9 Depreceted
 */
class DB_Class {
    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect()
    {
        if( isset($GLOBALS['METADATA']) )
        {
            $_dbconfig = @$GLOBALS['METADATA']->setup->db;
            
            if($_dbconfig)
            {
                try
                {
                    $_hosting = $_dbconfig->server;
                    $_username = $_dbconfig->username;
                    $_password = $_dbconfig->password;
                    $_database = $_dbconfig->table;

                    @$_connecting = new mysqli($_hosting, $_username, $_password, $_database);

                    if (mysqli_connect_errno()) {
                        die("Koneksi MySQL gagal: " . mysqli_connect_error());
                    }
                    elseif($_connecting->connect_error) {
                        die('Database tidak tersambung. ' . $_connecting->connect_error);
                    }
                    else
                    {
                        return $_connecting;
                    }
                }
                catch(Exception $e)
                {
                    throw new DBCException($e->getMessage());
                }
            }
            else
            {
                throw new DBCException('Setup db is undifined.');
            }
        }
        else
        {
            throw new DBCException('Metada is empty.');
        }
    }
}

/**
 * DBCException extends
 */
class DBCException extends Exception{}
?>