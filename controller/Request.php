<?php
namespace controller\Request;

/**
 * This class is basically a wrapper class on of the $_REQUEST super global.
 * Note that it is also helpful for request testing
 * 
 * @package controller
 */
class Request{
    /**
     * this variable is private thus only accessible from or by methods within the class only(is see no importance for this)
     * More importantly it holds the $_REQUEST value, standing in its place
     * @var array
     */
    private $request = array();
    /**
     * this variable will hold the single instance of Request that can exist. I think its important to
     * ensure that the request object remains a singleton so as to obtain same value regardless of where
     * an instance of request is initialized
     * @var string
     */
    static $instance;
    /**
     * Declared private to ensure it remain a singleton
     * @param null
     * @return Request
     */
    private function __construct(){
        $this->request = $_REQUEST;
    }

    static function getInstance(){
        if(!isset(self::$instance)){
        self::$instance = new self();
            return self::$instance;
        }
        return self::$instance;
    }

    function setProperty($key, $value){
        $inst = self::getInstance();
        $inst->request[$key] = $value;
     }

     function getProperty($key){
         $inst = self::getInstance();
         if(!empty($inst->request[$key])){
             return $inst->request[$key];
         }else{
             //Return a null would be important in ensuring that admin or anyone else is taken to home 
             return null;
         }
     }
}

/*$req = Request::getInstance();
$req->setProperty('action', 'show_AddVenue');
$val = $req->getProperty('action');
echo $val;*/
?>