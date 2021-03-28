<?php
namespace mapper\StudentMapper;

use mapper\Mapper\Mapper as Mapper;

require_once('Mapper.php');

/**
 * This class will practically handle Everything in and around managing student(s) information
 * It might get bloated over time but that will be considerered later on
 * @package mapper
 * 
 * @author Israel Immanuel Ibrahim
 */

class StudentMapper extends Mapper{
    /**
     * These set Of prepared statements waiting to be initialized  in the constructor
     * will deal with student details
     */
    private $selectStudentDetails;
    private $insertStudentDetails;
    private $updateStudentDetails;
    /**
     * These will deal with Student Financial information
     */
    private $selectStudentFinance;
    private $insertStudentFinance;//NOT needed
    private $updateStudentFinance;

    private $selectStudentPerformance;
    private $updateStudentPerformance;
    private $insertStudentPerformance;//NOT needed

    //later analyze need to access the student_average table from without the student_performance table
    
    /**
     * These set will deal with batch operations such as changin semester of entire cohort, checking
     * fee of complete cohorts etc
     */
     private $batchUpdateSession;

     /**
      * This set will focus on batch statistical analysis of matters concering finance and performance
      */
      private $batchFinance;
      private $batchPerfromance;

    function __construct(){
        parent::__construct();
        $this->selectStudentDetails = $this->pdo->prepare("SELECT *FROM student WHERE student_id=?");
        $this->updateStudentDetails = $this->pdo->prepare("UPDATE student SET course_id=?,
        student_admission_year=?,student_email=?,student_first_name=?,student_id=?,student_middle_name=?, student_semester=?,
        student_year=? WHERE student_id=?");
        $this->insertStudentDetails = $this->pdo->prepare("INSERT INTO student(course_id, student_admission_year, student_email,
        student_first_name, student_id, student_middle_name, student_password, student_semester, student_year) 
        VALUES(?,?,?,?,?,?,?,?,?)");
        
        $this->selectStudentFinance = $this->pdo->prepare("SELECT  student.student_id,student.student_middle_name,student.course_id,
        student_finance.paid,student_finance.fee_course_link_id,student.student_semester,student.student_year,fee_course_link.fee,
        student_finance.amount_due FROM student,student_finance,fee_course_link WHERE student.student_id=? AND 
        student_finance.student_id=? AND fee_course_link.fee_course_link_id=? AND student_finance.fee_course_link_id=?");
        //$this->insertStudentFinance = $this->pdo->prepare("INSERT INTO student_finance(paid) VALUES(?) WHERE student_id=?");
        $this->updateStudentFinance = $this->pdo->prepare("UPDATE student_finance SET paid=? WHERE student_id=? AND 
        fee_course_link_id=?");
        $this->updateStudentFinanceAmountDue = $this->pdo->prepare("UPDATE student_finance SET amount_due=? WHERE student_id=?
        AND fee_course_link_id=?");
    }

    function selectStudentDetails($student_id){
        $this->selectStudentDetails->execute([$student_id]);
        $res = $this->selectStudentDetails->fetch();
        return $res;
    }

    function updateStudentDetails($updateInfo){
        $this->updateStudentDetails->execute($updateInfo);
        $count = $this->updateStudentDetails->rowCount();
        return "update ".$this->queryStatus($count);
    }

    function insertStudentDetails($insertInfo){
        try{
        $this->insertStudentDetails->execute($insertInfo);
        }catch(PDOException $e){
            throw $e;
        }
        $count = $this->insertStudentDetails->rowCount();
        return "insert ".$this->queryStatus($count);
    }
    /**
     * This is one hell of a method
     * NOTE that if any error is made in the finance records, one would have to manually access all row records of the student having 
     * financial error, this is the paid and amount_due fields upto the last one, in the last row one may change only the paid and 
     * the amount due will update automatically using the layed down formula
     */

    function selectStudentFinance($student_id){
        //lets check if the finance row exists
        //first acquire student year, semester an course_id(this will be used in the fee_couse_link table)


        /**
         * First this student table will be accessed to obtain information beloinging  to the specified id.Note that most of the 
         * infomation obtained at this point will be used continuously across this long function mainly to ensure consistency in
         * searching for data from other tables e.g fee_course_link table,student_finance table and in the automatic creation of 
         * new columns when year or semester or both changes
         */
        $selectStudentStmt = $this->pdo->prepare("SELECT *FROM student WHERE student_id=?");
        $selectStudentStmt->execute([$student_id]);
        $studentInfo = $selectStudentStmt->fetchAll();
        $course_id = $studentInfo[0]['course_id'];
        $year = $studentInfo[0]['student_year'];
        $semester = $studentInfo[0]['student_semester'];
        $studentAdmission = $studentInfo[0]['student_admission_year'];
        $courseId = $studentInfo[0]['course_id'];
        $fclId = $studentAdmission.$courseId.$year.$semester;
        /**
         * Then the student_finance table is accessed and using data from the student table and param it is ensured that data from the 
         * student_finance table is legitimate.
         * From this table a rowCount() is made,this is to check whether the specified student in the parameter has a row in the 
         * student_finance table(Note that rows are created only if a student manages to go to the next session).A couple variations
         * are considered:
         * (a)For the first time a student is entered into the system, it would automatically follow that the attempt to fetch values
         * from the table would bring exceptions(Note that this is mitigated by use of ternary operator).
         * Further explanations of the variations that will arise depending on rowCount() result are explained in further commments
         */
        $selectStudentFinance = $this->pdo->prepare("SELECT *FROM student_finance WHERE student_id=? AND student_year=? AND 
        student_semester=?");
        $selectStudentFinance->execute([$student_id, $year, $semester]);
        $count = $selectStudentFinance->rowCount();
        /**
         * This variables will hold values only after a row has been created for a specific student otherwise the acquier a default
         * null value from the ternary operator.
         * Note also that this vairables are not useful until a row for the student is created
         */
        $studentFinanceInfo = $selectStudentFinance->fetchAll();
        $studentFinanceFee = (!empty($studentFinanceInfo[0]['fee_course_link_id']))?$studentFinanceInfo[0]['fee_course_link_id']:null;
        $studentFinancePaid = (!empty($studentFinanceInfo[0]['paid']))?$studentFinanceInfo[0]['paid']:null;
        $studentAmountDue = (!empty($studentFinanceInfo[0]['amount_due']))?$studentFinanceInfo[0]['amount_due']:null;
        /**
         * This table is queried to obtain its values which will also be used continuously across many lines. Not that it also
         * utilizes the 'fclId parameter which is constructed from data from the student table and it uniquely identifies a row
         * and thus helps in obtaining real fee value which will be used and fee_course_link_id which will also be useful to fill
         * student_finance table as foreign key.
         */
        $selectFeeCourseLink = $this->pdo->prepare("SELECT *FROM fee_course_link WHERE fee_course_link_id=?");
        $selectFeeCourseLink->execute([$fclId]);
        $feeCourseLinkInfo = $selectFeeCourseLink->fetchAll();
        $feeCourseLinkFee = $feeCourseLinkInfo[0]['fee_course_link_id'];
        $feeCourseLinkRealFee = $feeCourseLinkInfo[0]['fee'];

            /**
             * Execution will enter this block only if the specified row exists in the student_finance table, this is confirmed by
             * using rowCount(). Otherwise exectuion will skip this block and first create the column.
             * 
             */
        if($count == 1){
            //each time there is a check let us enure consistency with the fee_course_link_table by checking if the 
            //fee_course_link_id match
            //This part is applicable during the entire duration in which a student maintains in a certain semester.

            /**
             * NOTE that this SECTION is important for the continuous update of rows as student_id makes various payments that alter
             * amount_due in student_finance table
             * 
             * If the specified row exists the code below accesses the student_finance running the query below.
             * Using rowCount() the query might find multiple rows or find only one.
             */
            $preStudentFinance = $this->pdo->prepare("SELECT *FROM student_finance WHERE student_id=?");
            $preStudentFinance->execute([$student_id]);
            $preStudentFinanceInfo = $preStudentFinance->fetchAll();
            $preCount = $preStudentFinance->rowCount();
            //execution enters here only if there is a previous

            /**
             * Execution will Enter this block only if there exists more than two rows in the student_finance table belonging to 
             * the student with the specified id, Why? this is because the code below will first attempt to fetch amount_due of the
             * previous row and add this to the currentFee and minus currentPaid(this calculation maintains consistency)
             * Also NOTE that this code only starts running once the number of rows belongin to a specific student_id
             * in student_finance table become more than two(below this number another block will handle the logic)
             * 
             */
            if($preCount > 1){
                /**
                 * A simple way of ensuring previous row index is created.Accessing the previous is important for rendering the 
                 * currentAmountDue = prevAmountDue+currentFee-currentFeePaid formula complete
                 */
                $preCount = $preCount-2;
               /**
                * Acquire previous amount due
                */
                $preStudentAmountDue = $preStudentFinanceInfo[$preCount]['amount_due'];
                /**
                 * The block below shows how to handle the 'carry forward' mechanism incase someone pays less fee than is expected
                 */
                if($preStudentAmountDue > 0){
                    $currentStudentAmountDue = $preStudentAmountDue + $feeCourseLinkRealFee - $studentFinancePaid;
                    /**
                     * This check is constantly done to ensure consistency i.e each time a payment is done amount due would be
                     * immediately updated.
                     */
                    if($studentAmountDue != $currentStudentAmountDue){
                        /**
                         * Note that an UPDATE is done because that specified row already EXISTS
                         */
                        $updateStudentFinance = $this->pdo->prepare("UPDATE student_finance SET fee_course_link_id=?, amount_due=?
                        where student_id=? AND student_year=? AND student_semester=?");
                        $updateStudentFinance->execute([$feeCourseLinkFee,$currentStudentAmountDue, $student_id, $year, $semester]);
                    }

                    /**
                     * this method does the final touch of executing the main selectStudent finance defined in the constructor.
                     * Its only weakness is that it only fetches data of the very current session. Another more general method will
                     * be created
                     * But taking into account the enormous amount of work CONSISTENCY ASSURANCE this method does it must be called
                     * in other methods that utilize or fetch finance information.
                     * Also not that it is a candidate for encapsulation due to its repetitive nature
                     */
                    $this->selectStudentFinance->execute([$student_id,$student_id,$fclId,$fclId]);
                    $res = $this->selectStudentFinance->fetchAll();
                    return $res[0];
                    /**
                     * This block only specifies action to take incase student makes an overpay
                     */
                }else if($preStudentAmountDue < 0){
                    $currentStudentAmountDue = $feeCourseLinkRealFee+($preStudentAmountDue) - $studentFinancePaid;
                    if($studentAmountDue != $currentStudentAmountDue){
                        $updateStudentFinance = $this->pdo->prepare("UPDATE student_finance SET fee_course_link_id=?, amount_due=?
                        where student_id=? AND student_year=? AND student_semester=?");
                        $updateStudentFinance->execute([$feeCourseLinkFee,$currentStudentAmountDue, $student_id, $year, $semester]);
                    }

                    
                    $this->selectStudentFinance->execute([$student_id,$student_id,$fclId,$fclId]);
                    $res = $this->selectStudentFinance->fetchAll();
                    return $res[0];
                }//else if($preStudentAmountDue == 0){
                    /**
                     * This block will execute if the student pays exactly the amount owed to the instituion thus no overpay or 
                     * underpay
                     */
                //}
            }
            /**
             * NOTE that i have intentionally left out the  execution to follow incase exact fee is paid. Because the condition for
             * an exact paid amount will not be found exectuion will automatically exit and utilize the code below. Note that this
             * also saves on lines of code(but this execution only happens if row already exists). Or rather it utilizes the data
             * it was initialized with previously on creation
             * This code block executes if the number of row is equal to one. This would be the case either for a student joining the
             * institution for the first time and is paying fee.
             * It also maintains consistency i.e amount due changes accordingly if student pays fee;
             * 
             * 
             */
            $realFee = $studentAmountDue + $studentFinancePaid;
            if($studentFinanceFee != $feeCourseLinkFee || $realFee != $feeCourseLinkRealFee){
                $amountDue = $feeCourseLinkRealFee - $studentFinancePaid;
                $updateStudentFinance = $this->pdo->prepare("UPDATE student_finance SET fee_course_link_id=?, amount_due=?
                  where student_id=? AND student_year=? AND student_semester=?");
                $updateStudentFinance->execute([$feeCourseLinkFee,$amountDue, $student_id, $year, $semester]); 
            }
            

            $this->selectStudentFinance->execute([$student_id,$student_id,$fclId,$fclId]);
            $res = $this->selectStudentFinance->fetchAll();
            return $res[0];
        }else{
            /**
             * NOTE that this SECTION is important for the insertion of non existent rows and starting them of with the right values
             * 
             * Execution will enter this block only if the specified row does not exist, this part creates the needed row.
             * student_finance
             * The code below checks if more rows belonging to specified student exists using rowCount()
             */
            $preStudentFinance = $this->pdo->prepare("SELECT *FROM student_finance WHERE student_id=?");
            $preStudentFinance->execute([$student_id]);
            $preStudentFinanceInfo = $preStudentFinance->fetchAll();
            $preCount = $preStudentFinance->rowCount();
            /**
             * Note this block is important and clears the matter of creation of a new row for a specific student_id who had cleared
             * the exact fee, it starts afresh attempting to fetch the previous obviously finding it to be null or zero thus each
             * new row of a continuing student starts of perfectly, even with or without over and underpay
             * 
             * It creates the new row on a new year or semester or both initializing the correct amount due.Note that all further
             * new rows to be created upto the forth year are handled in this section
             */
            if($preCount > 0){
                $preCount = $preCount-1;
                $preStudentAmountDue = $preStudentFinanceInfo[$preCount]['amount_due'];
                /**
                 * This block executes if in the previous semester there was an underpay
                 */
                if($preStudentAmountDue > 0){
                    /**
                     * You may wonder why studentAmountPaid is not utilized in the formula, well, this is because at the creation
                     * of a new row it would be impossible that a student had paid and if student paid it would be impossible to 
                     * process the payment as the row did not exist in the first place
                     */
                    $currentStudentAmountDue = $preStudentAmountDue + $feeCourseLinkRealFee;
        
                         $insertStudentFinance = $this->pdo->prepare("INSERT INTO student_finance(student_id, student_year, 
                         student_semester, amount_due, fee_course_link_id) VALUES(?,?,?,?,?)");
                         $insertStudentFinance->execute([$student_id, $year, $semester,$currentStudentAmountDue, $feeCourseLinkFee]);
            
                                
                $this->selectStudentFinance->execute([$student_id,$student_id,$fclId,$fclId]);
                $res = $this->selectStudentFinance->fetchAll();
                return $res[0];
                /**
                 * This block executes if instead there was an overpay in teh previous semester
                 */
                }else if($preStudentAmountDue < 0){
                    $currentStudentAmountDue = $feeCourseLinkRealFee+($preStudentAmountDue);
            
                        $insertStudentFinance = $this->pdo->prepare("INSERT INTO student_finance(student_id, student_year, 
                        student_semester, amount_due, fee_course_link_id) VALUES(?,?,?,?,?)");
                        $insertStudentFinance->execute([$student_id, $year, $semester,$currentStudentAmountDue, $feeCourseLinkFee]);
            
                                
                    $this->selectStudentFinance->execute([$student_id,$student_id,$fclId,$fclId]);
                    $res = $this->selectStudentFinance->fetchAll();
                    return $res[0];
                }   
            }

            //this part is important if this row is being created for the first time for a paricular student.
            //This happens when a student is first registered or moves to another semester or year or both
            $insertStudentFinance = $this->pdo->prepare("INSERT INTO student_finance(student_id, student_year, student_semester,
            amount_due, fee_course_link_id) VALUES(?,?,?,?,?)");
            $insertStudentFinance->execute([$student_id, $year, $semester,$feeCourseLinkRealFee, $feeCourseLinkFee]);

            $this->selectStudentFinance->execute([$student_id,$student_id,$fclId,$fclId]);
            $res = $this->selectStudentFinance->fetchAll();
            return $res[0];
        }

    }
    //Notice that insert is not applicable, ONLY the update statement
    function insertStudentFinance($amount, $studentId){
        $this->insertStudentFinance->execute([$amount,$studentId]);
        $count = $this->insertStudentFinance->rowCount();
        return "insert ".$this->queryStatus($count);
    }
    /**
     * this function takes in studentId and studentUnitCourseLinkId to be more specific and select a unique row
     */
    function updateStudentFinancePaid($amount, $studentId,$studentUnitCourseLinkId){
        /**
         * Note that only running updateStudentFinancePaid doesn't update amount due, you would also then have to run 
         * selectStudentFinance
         */
        $this->updateStudentFinance->execute([$amount,$studentId,$studentUnitCourseLinkId]);
        /**
         * To ensure amountDue is equally updated after running an update on the paid amount run selecStudentFinance method
         */
        $this->selectStudentFinance($studentId);
        $count = $this->updateStudentFinance->rowCount();
        return "update ".$this->queryStatus($count);
    }
    /**
     * In the event of errors arising in the system this function would be important in altering amount due records, it also
     * utilizes the candidate keys studentId and studentUnitCourseLinkId to uniquely identify a row
     */
    function updateStudentFinanceAmountDue($amount, $studentId,$studentUnitCourseLinkId){
        $this->updateStudentFinanceAmountDue->execute([$amount,$studentId,$studentUnitCourseLinkId]);
        $count = $this->updateStudentFinance->rowCount();
        return "update ".$this->queryStatus($count);
     }
     /**
      * This method will ensure unit registration by a student to ensure a row is created in student_performance table which a 
      *lecurer will use when adding the student's marks
      */
     function registerUnit($studentId,$unitId,$year=null,$semester=null){
         /**
          * Note that besides fetching the amount_due the selectStudentFinance is vital in objtaining other crucial data needed
          *such as student semester, year etc which are used further down the function for efficiency and reducing code duplication
          */
         $studentFinanceInfo = $this->selectStudentFinance($studentId);
         $studentAmountDue = $studentFinanceInfo['amount_due'];
         $studentSemester = $studentFinanceInfo['student_semester'];
         $studentYear = $studentFinanceInfo['student_year'];
         $studentCourseId = $studentFinanceInfo['course_id'];
         if($studentAmountDue == 0 || $studentAmountDue < 0 ){
             echo "Umemaliza Madeni <br>";
            /**
             * After the financial check proceed to check if a row with the specified unit in the parameter exists, if it exists it 
             * means it was already registered, otherwise create a row with belonging to the student specified with the unit to be
             * registered 
             * 
             * It would also seem shrewd to ensure that the student is eligible for the unit so specified
             * To ensure eligibility, using student information check in the unit_course_link table if a row corresponding to the 
             * student data is returned, if not so quit execution and notify the student(Note this might only be necessary in test
             * enviroment, but also note that the exact code below can be used to fill the dropdown for units selection in front-end).
             */
            $selectUnitCourseLink = $this->pdo->prepare("SELECT *FROM unit_course_link WHERE year=? AND semester=? AND course_id=?
            AND unit_id=?");
                    /**
                    * NOTE that the use of $year and $semester may only be applicable in testing for the purpose of quick row update,
                    * production, work with the current student's year and semester instead as this is more logical. Though still 
                    * note that for manual update of unit to be registered it will be necessary to manually insert the year and
                    *  semester
                     */
            $selectUnitCourseLink->execute([$studentYear, $studentSemester, $studentCourseId, $unitId]);
            $isUnitMineCount = $selectUnitCourseLink->rowCount();
            if($isUnitMineCount < 1){
                echo "You are not Eligible to register for the specified unit.They unit may not be relevant to your course in
                 this session<br>";
                return;
            }

             $selectStudentPerformance = $this->pdo->prepare("SELECT *FROM student_performance WHERE student_id=? AND unit_id=?");
             $selectStudentPerformance->execute([$studentId, $unitId]);
             $count = $selectStudentPerformance->rowCount();
             if($count == 1){
                 /**
                  * Code Exection enters here if the specified row which can only be one in the entire student_performance table
                  *is found, this means the unit is already registered
                  */
                 echo "This unit is already Registered<br>";
             }else{
                 /**
                  * Execution enters here if the specified unit does not exist in student_performance table yet it should exist
                  *the row is created with all fields acquiring relevant data at creation
                  */
                  $insertStudentPerformance = $this->pdo->prepare("INSERT INTO student_performance(student_id,student_year,
                  student_semester, unit_id, marks) VALUES(?,?,?,?,?)");
                  $insertStudentPerformance->execute([$studentId,$studentYear,$studentSemester,$unitId, null]);
             }
         }else{
             echo "Lipa madeni kijana <br>";
         }
     }

     /**
      * This function will handle the process of adding (a) students' marks to the database.A lecturer or admin will be able to 
      * execute this function
      */
     function addStudentMarks($studentId,$unitId,$year,$semester,$marks,$totalUnits){
         /**
          * Only access the student's performance table if fee is already cleared
          * Note that an update statement is what we will use to capture a very specific row and update the marks section. There is
          * a chance that the row might not exist meaning the student cleared fee but has not registered the unit
          *
          * Note that besides fetching the amount_due the selectStudentFinance is vital in obtaining other crucial data needed
          * such as student semester, year etc which are used further down the function for efficiency and reducing code duplication
          * It also further ensures the student's finance table is well updated
          */
         $studentFinanceInfo = $this->selectStudentFinance($studentId);
         $studentAmountDue = $studentFinanceInfo['amount_due'];
         $studentSemester = $studentFinanceInfo['student_semester'];
         $studentYear = $studentFinanceInfo['student_year'];
         $studentCourseId = $studentFinanceInfo['course_id'];

         if($studentAmountDue == 0 || $studentAmountDue < 0 ){
            $selectStudentPerformance = $this->pdo->prepare("SELECT *FROM student_performance WHERE unit_id=? AND student_year=? AND 
            student_semester=? AND student_id =?");
            $selectStudentPerformance->execute([$unitId,$year,$semester,$studentId]);
            $count = $selectStudentPerformance->rowCount();
            /**
             * Check if the specified row exists if so proceed to update the row, otherwise give a message
             */
            if($count == 1){
                $updateStudentPerformance = $this->pdo->prepare("UPDATE student_performance SET marks=? WHERE unit_id=? AND 
                student_year=? AND student_semester=? AND student_id=?");
                $updateStudentPerformance->execute([$marks, $unitId,$year,$semester,$studentId]);
                /**
                 * Update the student_average table after changing the marks
                 */
                $this->calculateAverage($studentId,$totalUnits,$year,$semester);
            }else{
                echo "It seems this unit has not been registered.<br>";
            }
         }else{
             echo "This student has not cleared fee.<br>";
         }
     }
     /**
      * This method selects every single performance detail of a student using the supplied student_id
      * @param $student_id
      * @return array representing all relevant rows containing student performance in each unit in a particular semester
      */
     function selectStudentPerformance($studentId){
        $selectStudentPerformance = $this->pdo->prepare("SELECT *FROM student_performance WHERE student_id=?");
        $selectStudentPerformance->execute([$studentId]);
        $res = $selectStudentPerformance->fetchAll();
        $count = $selectStudentPerformance->rowCount();
        
        if($count > 0){
            return $res;
        }else{
            echo "There is no record of this Student's performance. This may be due to unpayed school Fees or unregistered units or 
            both";
        }
     }

     /**
      * This method calculates the average performance based on the marks entered into the student_performance table. The method will
      * will take a digit will will be used to divide the sum of all the units.
      *
      * Basically the average should be calculated after all units have been entered but for this system, the average will be 
      * calculated each time a lecturer add a student's unit to the system. This ensures a consistency of sorts
      */
     function calculateAverage($studentId,$totalUnits,$year=null,$semester=null){
        $selectStudentDetails = $this->pdo->prepare("SELECT *FROM student WHERE student_id=?");
        $selectStudentDetails->execute([$studentId]);
        $studentInfo = $selectStudentDetails->fetch();
        $studentYear = $studentInfo['student_year'];
        $studentSemester = $studentInfo['student_semester'];
        $selectStudentPerformance = $this->pdo->prepare("SELECT marks FROM student_performance WHERE student_id=? AND 
        student_year=? AND student_semester=?");
        /**
         * NOTE that the use of $year and $semester may only be applicable in testing for the purpose of quick row update, for 
         * production, work with the current student's year and semester instead as this is more logical. Though still note that 
         * for manual update of average it will be necessary to manually insert the year and semester
         */
        $selectStudentPerformance->execute([$studentId, $year,$semester]);
        $res = $selectStudentPerformance->fetchAll();
        $count = $selectStudentPerformance->rowCount();
        if($count > 0){
            $sum = 0;
            foreach($res as $row){
                foreach($row as $key=>$value){
                    $sum = $sum + $value;
                }
            }
            $average = round($sum/$totalUnits);
            echo $average. "<br>";
            /**
             * The calculate average method will be utilized by the addStudentMarks() to ensure consistency. 
             * 
             * Thus from below a check is done to check if a row is available in student_average, if not it is created 
             * 
             */
            $selectStudentAverage = $this->pdo->prepare("SELECT *FROM student_average WHERE student_id=? AND student_year=? AND
            student_semester=?");
            $selectStudentAverage->execute([$studentId,$year,$semester]);
            $averageCount = $selectStudentAverage->rowCount();
            if($averageCount == 1){
                $updateStudentAverage = $this->pdo->prepare("UPDATE student_average SET student_average=? WHERE student_id=? AND 
                student_year=? AND student_semester=?");
                $updateStudentAverage->execute([$average,$studentId,$year,$semester]);
                return $average;

            }else{
                $insertStudentAverage = $this->pdo->prepare("INSERT INTO student_average(student_id,student_year,student_semester,
                student_average) VALUES(?,?,?,?)");
                $insertStudentAverage->execute([$studentId,$year,$semester,$average]);
                return $average;
            }
        }else{
            echo "There is no record of this Student's performance. This may be due to unpayed school Fees or unregistered units or 
            both";
        }
     }

     /**
      * Moves a student from one session to the next,there will be no manual movement unless necessitated. This method aims to 
      * reduce the hustle of confirming a cohorts semester before updating them and also ensures greater consistency and database
      * integrity.
      */
      function UpdateStudentSession($studentId){
          /**
           * Note that the students year and semester are of importance
           */
        $selectStudentDetails = $this->pdo->prepare("SELECT *FROM student WHERE student_id=?");
        $selectStudentDetails->execute([$studentId]);
        $studentInfo = $selectStudentDetails->fetch();
        $studentYear = $studentInfo['student_year'];
        $studentSemester = $studentInfo['student_semester'];
        /**
         * The method will check the students year and semester, if semester is equal to 1 then change semester to 2 
         * retaining the value of year.Note that year can only increase. If semester is equal to two, retrieve the value of year and
         * increment it then decrement the value of semester
         * 
         * Note that it will be necessary to to update the student_finance table thus the need to call selectStudentFinance() method
         */
        if($studentSemester == 1){
            $studentSemester++;
            $updateStudentSemester = $this->pdo->prepare("UPDATE student SET student_semester=? WHERE student_id=?");
            $updateStudentSemester->execute([$studentSemester,$studentId]);
            $this->selectStudentFinance($studentId);
        }else if($studentSemester == 2){
            $studentSemester--;
            $studentYear++;
            $updateStudentSession = $this->pdo->prepare("UPDATE student SET student_year=?, student_semester=? WHERE student_id=?");
            $updateStudentSession->execute([$studentYear,$studentSemester,$studentId]);
            $this->selectStudentFinance($studentId);
        }

      }

    
}

/*
$sm = new StudentMapper();
//$sm->updateStudentFinanceAmountDue(15000,"S13/6/2017", "2017S1311");
//$sm->updateStudentFinancePaid(45000,"S13/6/2017", "2017S1312");
$res = $sm->selectStudentFinance("S13/8/2017");
// echo "THIS is all ".$res["amount_due"]."<br>";
foreach($res as $key=>$value){
    echo $key. " = " . $value . "<br>";
}

$sm->registerUnit("S13/8/2017","COMP103");
$sm->addStudentMarks("S13/8/2017","COMP103",1,1,60,5);
$perf = $sm->selectStudentPerformance("S13/8/2017");
echo "<p></p>";

foreach($perf as $row){
    echo "<p></p>";
    echo "Student's Performance <br>";
    foreach($row as $key=>$value){
    echo $key. " = ". $value. "<br>";
    }
}

$sm->calculateAverage("S13/8/2017",5,1,1);

//$sm->UpdateStudentSession("S13/1/2017");
//$i_msg = $sm->insertStudentDetails(['S13','2017','marwa@gmail.com', 'marwa', 'S13/8/2017','marwa','S13/8/2017', 1,1,]);
//echo $i_msg."<br>";
//$sm->registerUnit("COMP101", "S13/8/2017");


*/

?>