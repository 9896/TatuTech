<?php
/**
 * This page will handle the linking of coureses to units including their semester and year
 */
include_once('templates/admin_header.php')
?>
<div class="">
        <div class="form login admin">
        <p><h1>Link Units to Courses</h1></p>
    <form action="" method="post" autocomplete="">

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
        <?php echo $request->getProperty('unit_error') ?? ''; ?> 
        <p>
           <label for="unit">Unit: </label>
           <select name="unit" id="unit" multiple>
           <!--A php loop will be placed here to loop thru all available units-->
                <option label=""></option>
               <option value="COMP100">Introduction to programming asdfasdfasdfasdf</option>
               <option value="STAT100">Introduction to statistics</option>
           </select>
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
       <input type="hidden" name="action" value="UnitCourseLink">
        <p class="submit_p"><input type="submit" value="Link" class="submit_b"></p>
    </form>
        <p>Note you can <em>Batch Link</em> units to courses <a href="#">Learn more</a></p>
        </div>
    </div>

<?php
include_once('templates/admin_footer.php')
?>