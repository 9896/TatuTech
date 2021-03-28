<?php
namespace controller\AddCourse;

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
class AddCourse extends Command{
    /**
     * Serves the veiw and command execution
     * 
     * @see Command::execute()
     * 
     */
    function execute(Request $request){
        $action = $request->getProperty('action');
        if($action == "show_AddCourse"){
            include_once('add_course.php');
        }else{
            /**
             * Utilizing addCourse due to their similar nature
             */
            /**
             * Add course to the database
             */
            $auth = new Authentication();
            list($bool, $res) = $auth->filtershortForm($_POST,$request);
            if($bool != true){
                /**
                 * Form pops up with error information
                 */
            }else{
                $courseId = $res['id'];
                $courseName = $res['name'];
                
                $um = new CourseMapper();
                $um->insertCourse($courseId,$courseName);
                $request->setProperty('success', '<p class="success">Course Added Successfully</p>');
            }
            include_once('add_course.php');

        }
    }
}

?>