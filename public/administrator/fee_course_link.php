<?php
/**
 * This page will handle the linking of coureses to units including their semester and year
 */
include_once('templates/admin_header.php');
?>
<div class="">
        <div class="form login admin">
        <p><h1>Link Fee to Course</h1></p>
    <form action="" method="post" autocomplete="">
        <!--Only available units will be fetched and utilized in the linking-->  
        <?php echo $request->getProperty('success') ?? ''; ?> 

       <?php echo $request->getProperty('course_error') ?? ''; ?>       
       <p>
           <label for="course">Course: </label>
           <select name="course" id="course">
           <!--A php loop will be placed here for all the courses-->
               <option label=""></option>
               <option value="S13">Computer Science</option>
               <option value="S01">Statistics</option>
           </select>
       </p>
               <!--Only available units will be fetched and utilized in the linking--> 
        <?php echo $request->getProperty('fee_error') ?? ''; ?>
        <p>
           <label for="fee">Fee: </label>
            <input type="text" id="fee" name="fee" class="input">
       </p>  

       <?php echo $request->getProperty('year_error') ?? ''; ?>
       <p>
           <label for="year">Year: </label>
           <select name="year" id="year">
           <!--A php loop will be placed here for all the courses-->
                <option label=""></option>
               <option value="1">Y1</option>
               <option value="2">Y2</option>
               <option value="3">Y3</option>
               <option value="4">Y4</option>
           </select>
       </p>

       <?php echo $request->getProperty('semester_error') ?? ''; ?>
       <p>
           <label for="semester">Semester: </label>
           <select name="semester" id="semester">
           <!--A php loop will be placed here for all the courses-->
                <option label=""></option>
               <option value="1">S1</option>
               <option value="2">S2</option>
           </select>
       </p>

       <?php echo $request->getProperty('calender_year_error') ?? ''; ?>
       <p><label for="calender_year">Calender Year</label>
       <input type="text" id="calender_year" name="calender_year" class="input">
       </p>

       <?php echo $request->getProperty('fee_course_link_id_error') ?? ''; ?>
       </p>
       <p><label for="fee_course_link_id">Fee Course Link ID:</label>
       <input type="text" id="fee_course_link_id" name="fee_course_link_id" class="input">
       </p>

       <input type="hidden" name="action" value="FeeCourseLink">
        <p class="submit_p"><input type="submit" value="Link" class="submit_b"></p>
    </form>
        <p>Note you can <em>Batch Link</em> units to courses <a href="#">Learn more</a></p>
        </div>
    </div>

<?php
include_once('templates/admin_footer.php');
?>