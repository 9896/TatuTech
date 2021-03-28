<?php
namespace controller\UnitCourseLink;

use auth\Authentication\Authentication as Authentication;
use mapper\UnitMapper\UnitMapper as UnitMapper;
use controller\Command\Command as Command;
use controller\Request\Request as Request;

require_once('../../auth/Authentication.php');
require_once('../../mapper/UnitMapper.php');
require_once('Request.php');
require_once('Command.php');

/**
 * This class depending on the request serves up the form or adds a student into the database
 * @package command
 */
class UnitCourseLink extends Command{
    /**
     * Serves up the correct view or adds the student to the database
     * 
     * @see Command::execute()
     * 
     */
    function execute(Request $request){
        $action = $request->getProperty('action');
        if($action == "show_UnitCourseLink"){
            include_once('unit_course_link.php');
        }else{
            /**
             * Will handle adding a student to the database inclusive of checking possible input mistakes and return Error message 
             * Notice that some input checks will apply to all inputs while others will require specific sanitization using probably
             * REGEX
             */
            $auth = new Authentication();
            list($bool,$res) = $auth->filterLongForm($_POST,$request,$auth->unitCourseLinkFilter);
            if($bool != true){
            /**
             * if an error is found, the form show up again with error messages at the top of each incorrect input with no insetion
             * into the data base taking place, simply execution flows the bottom where the form is displayed
             */
            
            
            }else{
                /**
                 * Note that all the data beolow is basically clean data that has been sanitized and validated
                 */
                $Unit = $res['course'];
                $unit = $res['unit'];

                $year = $res['year'];
             
                $semester = $res['semester'];
                

                
                try{
                $sm = new UnitMapper();
                $sm->unitCourseLink($unit, $Unit, $year, $semester);
                }catch(PDOException $e){
                    $request->setProperty('duplicate', '<p class="error">Sorry it seems the specified ID is already taken</p>');
                }
                $request->setProperty('success', '<p class="success">Unit linked to Unit Successfully</p>');
            }
            include_once('unit_course_link.php');
        }
    }
}

?>