<?php
namespace controller\CommandResolver;

use controller\Request\Request as Request;
use controller\AddAdmin\AddAdmin as AddAdmin;
use controller\AddStudent\AddStudent as AddStudent;
use controller\AddLecturer\AddLecturer as AddLecturer;
use controller\Command\Command as Command;
use controller\AdminHome\AdminHome as AdminHome;
use ReflectionClass;
use Exception;


require_once('Request.php');
require_once('AddAdmin.php');
require_once('AddStudent.php');
require_once('AddLecturer.php');
require_once('Command.php');
require_once('AdminHome.php');

/**
 * This class is responsible for the picking the correct {@link Command} class. This is done by a check
 * that sees whether a specific file(the command) exists, if so it is returned.
 * @package command
 */
class CommandResolver{
    /**
     * privatise the constructor as i fail to see any situation in which its use will be needed
     * @param null
     * @return void
     */
    private function __construct(){}
    /**
     * This function will be static to reduce the need for seemingly unnecessary class instantiation
     * @param $request Request used as a wrapper of the global Request object. It provides the request
     * value
     * @return Command object.If the specified command not found a default command will otherwise be
     * returned
     */
    static function getCommand(Request $request){
        
        $baseCmd = new ReflectionClass('controller\Command\Command');
        $ds = DIRECTORY_SEPARATOR;
        $package = "controller";
        $action = $request->getProperty('action');
        if(empty($action)){return new AdminHome();}
        //$strln = strlen($action);
        $show = substr($action,0,5);
        if($show == "show_"){
            $action = substr($action,5,25);
        }
        //getcwd().$ds.$action.".php"; Note that this would result in an error because this script
        //actually executes in foreign lands
        $path = $_SERVER['DOCUMENT_ROOT'].$ds.'tatutech'.$ds.'controller'.$ds.$action.'.php';
        if(file_exists($path)){
            require_once($path);
            $cmd = new ReflectionClass($package.$ds.$action.$ds.$action);
           if($cmd->isSubClassOf($baseCmd)){
               return $cmd->newInstance();
           }else{
               echo "This command in not an subclass of the Command super class";
           }
        }else{
            //echo $path;
            throw new Exception();
        }
    }

}

//____________TEST_____________
/*
$req = Request::getInstance();
$req->setProperty('action', 'show_AddStudent');
$cmd = CommandResolver::getCommand($req);

$cmd->execute($req);
*/
?>