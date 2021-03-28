<?php
namespace controller\FeeCourseLink;

use auth\Authentication\Authentication as Authentication;
use controller\Command\Command as Command;
use controller\Request\Request as Request;
use mapper\CourseMapper\CourseMapper as CourseMapper;

require_once('../../mapper/CourseMapper.php');
require_once('../../auth/Authentication.php');
require_once('Request.php');
require_once('Command.php');

/**
 * This class depending on the request serves up the form or adds a student into the database
 * @package command
 */
class FeeCourseLink extends Command{
    /**
     * Serves up the correct view or adds the student to the database
     * 
     * @see Command::execute()
     * 
     */
    function execute(Request $request){
        $action = $request->getProperty('action');
        if($action == "show_FeeCourseLink"){
            include_once('fee_course_link.php');
        }else{
            /**
             * Will handle adding a student to the database inclusive of checking possible input mistakes and return Error message 
             * Notice that some input checks will apply to all inputs while others will require specific sanitization using probably
             * REGEX
             */
            $auth = new Authentication();
            list($bool,$res) = $auth->filterLongForm($_POST,$request,$auth->feeCourseLinkFilter);
            if($bool != true){
            /**
             * if an error is found, the form show up again with error messages at the top of each incorrect input with no insetion
             * into the data base taking place, simply execution flows the bottom where the form is displayed
             */
            
            
            }else{
                /**
                 * Note that all the data beolow is basically clean data that has been sanitized and validated
                 */
                $course = $res['course'];
                $fee = $res['fee'];

                $feeCourseLinkId = $res['fee_course_link_id'];
                $calender = $res['calender_year'];
                $semester = $res['semester'];
                $year = $res['year'];

                
                try{
                $sm = new CourseMapper();
                $sm->feeCourseLink($course,$fee,$year,$semester,$feeCourseLinkId,$calender);
                }catch(PDOException $e){
                    $request->setProperty('duplicate', '<p class="error">Sorry it seems the specified ID is already taken</p>');
                }
                $request->setProperty('success', '<p class="success">Fee Linked to Course Successfully</p>');
            }
            include_once('fee_course_link.php');
        }
    }
}

?>