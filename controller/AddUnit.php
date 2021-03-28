<?php
namespace controller\AddUnit;

use auth\Authentication\Authentication as Authentication;
use controller\Command\Command as Command;
use controller\Request\Request as Request;
use mapper\UnitMapper\UnitMapper as UnitMapper;

require_once('../../auth/Authentication.php');
require_once('../../mapper/UnitMapper.php');
require_once('Request.php');
require_once('Command.php');

/**
 * This class depending on the request serves up the form or adds a student into the database
 * @package command
 */
class AddUnit extends Command{
    /**
     * Serves up the correct view or adds the student to the database
     * 
     * @see Command::execute()
     * 
     */
    function execute(Request $request){
        $action = $request->getProperty('action');
        if($action == "show_AddUnit"){
            include_once('add_unit.php');
        }else{
            /**
             * Add unit to the database
             */
            $auth = new Authentication();
            list($bool, $res) = $auth->filterShortForm($_POST,$request);
            if($bool != true){
                /**
                 * Form pops up with error information
                 */
            }else{
                $unitId = $res['id'];
                $unitName = $res['name'];
                
                $um = new UnitMapper();
                $um->insertUnit($unitId,$unitName);
                $request->setProperty('success', '<p class="success">Unit Added Successfully</p>');
            }
            include_once('add_unit.php');
        }
    }
}

?>