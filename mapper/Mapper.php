<?php
namespace mapper\Mapper;

use PDO;
/**
 * This class will only specify the pdo object, propterties to be inherited by subclasses and respective
 * methods
 * Note that this isn't essentially a mapper class as it does not do any true mapping, thus each method 
 * must do proper type checking
 */

abstract class Mapper{
    /**
     * This variable will be used by all child classes to acess the database
     * @var string
     */
    protected $pdo;
    /**
     * It is in this method that pdo object will be instatiated and made ready for use by all child classes
     * @param null
     * @return Mapper object
     */
    function __construct(){
        $dsn = "mysql:host=localhost;dbname=tatutech";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        try{
        $this->pdo = new PDO($dsn, "root", "", $options);
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    function queryStatus($count){
        if($count > 0){
            return "Succesfull";
        }else if($count < 0){
            return "An error Occured";
        }else if($count == 0){
            return "Query did Not run or Where clause could not find record or Nothing new in update";
        }
    }
    /**
     * Set of abstract methods to be implemented by child classes
     */
    //abstract function insert();
    //abstract function update();
    //abstract function delete();
    //abstract function find();
}

?>