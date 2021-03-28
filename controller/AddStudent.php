<?php
namespace controller\AddStudent;

use auth\Authentication\Authentication as Authentication;
use controller\Command\Command as Command;
use controller\Request\Request as Request;
use mapper\StudentMapper\StudentMapper as StudentMapper;

require_once('../../auth/Authentication.php');
require_once('../../mapper/StudentMapper.php');
require_once('Request.php');
require_once('Command.php');

/**
 * This class depending on the request serves up the form or adds a student into the database
 * @package command
 */
class AddStudent extends Command{
    /**
     * Serves up the correct view or adds the student to the database
     * 
     * @see Command::execute()
     * 
     */
    function execute(Request $request){
        $action = $request->getProperty('action');
        if($action == "show_AddStudent"){
            include_once('add_student.php');
        }else{
            /**
             * Will handle adding a student to the database inclusive of checking possible input mistakes and 
             * return Error message 
             * Notice that some input checks will apply to all inputs while others will require specific 
             * sanitization using probably
             * REGEX
             */
            $auth = new Authentication();
            list($bool,$res) = $auth->filterLongForm($_POST,$request,$auth->addStudentFilter);
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
                $admission = $res['admission'];
                $email = $res['email'];
                $firstName = $res['first_name'];
                $id = $res['id'];
                $middleName = $res['middle_name'];
                $password = $res['password'];
                $semester = $res['semester'];
                $year = $res['year'];

                
                try{
                $sm = new StudentMapper();
                $sm->insertStudentDetails([$course, $admission,$email,$firstName,$id, $middleName,$password,$semester, $year]);
                }catch(PDOException $e){
                    $request->setProperty('duplicate', '<p class="error">Sorry it seems the specified ID is already taken</p>');
                }
                $request->setProperty('success', '<p class="success">Student Added Successfully</p>');
            }
            include_once('add_student.php');
        }
    }
}


?>