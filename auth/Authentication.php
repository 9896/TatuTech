<?php
namespace auth\Authentication;

/**
 * This class will handle all sanitization and validation of every input from every form in the system
 * It will also handle login, logout, and sessions
 */
class Authentication{

    public $addStudentFilter;
    public $feeCourseLinkFilter;
    public $unitCourseLinkFilter;

    function __construct(){
        $this->addStudentFilter = array(
            "email" => FILTER_VALIDATE_EMAIL,
            "id" => FILTER_SANITIZE_STRING,
            "password" => FILTER_DEFAULT,
            "first_name" => FILTER_SANITIZE_STRING,
            "middle_name" => FILTER_SANITIZE_STRING,
            "admission" => FILTER_VALIDATE_INT,
            "course" => FILTER_DEFAULT,
            "year" => FILTER_DEFAULT,
            "semester" => FILTER_DEFAULT,
        );

        $this->feeCourseLinkFilter = array(
            "course" => FILTER_SANITIZE_STRING,
            "fee" => FILTER_VALIDATE_INT,
            "year" => FILTER_VALIDATE_INT,
            "semester" => FILTER_VALIDATE_INT,
            "calender_year" => FILTER_VALIDATE_INT,
            "fee_course_link_id" => FILTER_SANITIZE_STRING
        );

        $this->unitCourseLinkFilter = array(
            "course" => FILTER_SANITIZE_STRING,
            "unit" => FILTER_DEFAULT,
            "year" => FILTER_VALIDATE_INT,
            "semester" => FILTER_VALIDATE_INT
        );
    }
    /**
     * this method filters all the input  of add student formand return an error if found, 
     * otherwise returns true
     * @param $post which will be the array holding all input data
     * @return boolean true or false
     * 
     */
    
    function filterLongForm($post,$request,$postFilter){
        $trimmedPost = array();
        foreach($post as $key=>$value){
            $trim = trim($value);
            $trimmedPost[$key] = $trim;
        }
        

        //print_r($trimmedPost);

        $filteredInput = filter_var_array($trimmedPost, $postFilter);
        $error = "";
        foreach($filteredInput as $key => $value){
            if(empty($value)){
                $error = '<p class="error">Please Enter a valid '.$key.'</p>';
                /**
                 * Alternatively add each error message belonging to a specific input field into the 
                 * request object with the name attribute as key and the message as value
                 * 
                 */
                $key .= "_error";
                $request->setProperty($key,$error);
            }/*else{
                echo "$key = $value <br>";
            }*/
        }
        if(empty($error)){
            return array(true,$filteredInput);
        }else{
            return array(false,$error);
        } 
    }

    /**
     * This function filters input data of the addUnit form, not much going on, it just ensures an empty
     * is not added to the database
     */
    function filterShortForm($post, $request){
        $trimmedPost = array();
        foreach($post as $key=>$value){
            $trim = trim($value);
            $trimmedPost[$key] = $trim;
        }
        /**
         * The data is not filtered because the unitId and unit name can take string values of any sort
         * They are only trimmed and checked to ensure no empty value is accepted
         * 
         */

        foreach($trimmedPost as $key => $value){
            /*
            * Note that the empty method used here ensures no empty data is accepted
            * 
            */
            if(empty($value)){
                $error = '<p class="error">Please Enter a valid '.$key.'</p>';
                /**
                 * Alternatively add each error message belonging to a specific input field into the 
                 * request object with the name attribute as key and the message as value
                 * 
                 */
                $key .= "_error";
                $request->setProperty($key,$error);
            }/*else{
                echo "$key = $value <br>";
            }*/
        }
        if(empty($error)){
            return array(true,$trimmedPost);
        }else{
            return array(false,$error);
        } 
    }


}
?>