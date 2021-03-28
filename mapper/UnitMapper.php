<?php
namespace mapper\UnitMapper;

use mapper\Mapper\Mapper as Mapper;

require_once('Mapper.php');

/**
 * This class inserts data into the units table
 */
class UnitMapper extends Mapper{

    private $insertUnitStmt;
    private $selectUnitStmt;
    private $updateUnitStmt;

    function __construct(){
        parent::__construct();
        $this->insertUnitStmt = $this->pdo->prepare("INSERT INTO unit(unit_id, unit_name) VALUES(?,?)");
        $this->unitCourseLinkStmt = $this->pdo->prepare("INSERT INTO unit_course_link(unit_id, course_id, year, semester) 
        VALUES(?,?,?,?)");
    }



    function insertUnit($unit_id, $unit_name){
        
        try{
        $this->insertUnitStmt->execute([$unit_id, $unit_name]);
        }catch(PDOException $e){
           echo $e->getMessage();
        }
        $count = $this->insertUnitStmt->rowCount();
        return $this->queryStatus($count);
    }

    function unitCourseLink($unitId,$courseId,$year,$semester){
        $this->unitCourseLinkStmt->execute([$unitId, $courseId, $year, $semester]);
    }
}
/*
$um = new UnitMapper();
$msg = $um->insertUnit("COMP200", "Discreet Mathematics Advanced Concepts");
echo $msg;

$um->unitCourseLink("COMP200", "S13",2,1);
*/
?>