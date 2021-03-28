<?php
namespace controller\AddLecturer;

use controller\Command\Command as Command;
use controller\Request\Request as Request;

require_once('Request.php');
require_once('Command.php');

/**
 * This class depending on the request serves up the form or adds a student into the database
 * @package command
 */
class AddLecturer extends Command{
    /**
     * Serves up the correct view or adds the student to the database
     * 
     * @see Command::execute()
     * 
     */
    function execute(Request $request){
        $action = $request->getProperty('action');
        if($action == "show_AddLecturer"){
            include_once('add_lecturer.php');
        }else{
            //add student to the database
        }
    }
}

?>