<?php
namespace mapper\CourseMapper;

use mapper\Mapper\Mapper as Mapper;

require_once('Mapper.php');

/**
 * This class inserts data into the units table
 */
class CourseMapper extends Mapper{

    private $insertCourseStmt;
    private $selectCourseStmt;
    private $updateCourseStmt;
    private $feeCourseLinkStmt;

    function __construct(){
        parent::__construct();
        $this->insertCourseStmt = $this->pdo->prepare("INSERT INTO course(course_id, course_name) 
        VALUES(?,?)");
        $this->feeCourseLinkStmt = $this->pdo->prepare("INSERT INTO fee_course_link(course_id, fee, 
        year,semester,fee_course_link_id, calender_year) VALUES(?,?,?,?,?,?)");
    }



    function insertCourse($unit_id, $unit_name){
        
        try{
        $this->insertCourseStmt->execute([$unit_id, $unit_name]);
        }catch(PDOException $e){
           echo $e->getMessage();
        }
        $count = $this->insertCourseStmt->rowCount();
        return $this->queryStatus($count);
    }

    function feeCourseLink($courseId,$fee,$year,$semester,$fee_course_link_id,$calender_year){
        $this->feeCourseLinkStmt->execute([$courseId, $fee, $year, $semester, $fee_course_link_id, 
        $calender_year]);
        
    }
}

/*
$um = new CourseMapper();
//$msg = $um->insertCourse("S11", "Economics and Statistics");
echo $msg;

$um->feeCourseLink("S13",38000,3,1,"2017S1331", 2019);
*/
?>