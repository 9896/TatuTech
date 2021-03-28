<?php
include_once('templates/admin_header.php');
?>
<div class="">
        <div class="form login admin">
        <p><h1>Add Student</h1></p>
        <form action="" method="post" autocomplete="">
        <?php echo $request->getProperty('success') ?? ''; ?> 

        <?php echo $request->getProperty('email_error') ?? ''; ?> 
       <p> <label for="email">Email:</label><input type="text" id="email" class="input" name="email" 
       value="<?php echo $_POST['email'] ?? ''; ?>"></p>

       <?php echo $request->getProperty('id_error') ?? ''; ?>
       <p><label for="id">Student ID: </label><input type="text" id="id" class="input" name="id"
       value="<?php echo $_POST['id'] ?? ''; ?>"></p>

       <?php echo $request->getProperty('password_error') ?? ''; ?>
       <p><label for="password">Password: </label><input type="password" id="password" class="input" name="password"
       value="<?php echo $_POST['password'] ?? ''; ?>"></p>

       <?php echo $request->getProperty('first_name_error') ?? ''; ?>
       <p><label for="first_name">First Name: </label><input type="text" id="first_name" class="input" name="first_name"
       value="<?php echo $_POST['first_name'] ?? ''; ?>"></p>

       <?php echo $request->getProperty('middle_name_error') ?? ''; ?>
       <p><label for="middle_name">Middle Name: </label><input type="text" id="middle_name" class="input" name="middle_name"
       value="<?php echo $_POST['middle_name'] ?? ''; ?>"></p>


       <?php echo $request->getProperty('admission_error') ?? ''; ?>
       <p><label for="yoa">Year of admission: </label><input type="text" id="yoa" class="input" name="admission"
       value="<?php echo $_POST['admission'] ?? ''; ?>"></p>

       <?php echo $request->getProperty('course_error') ?? ''; ?>
       <p>
           <label for="course">Course: </label>
           <select name="course" id="course">
           <!--A php loop will be placed here for all the courses-->
               <option label=""></option>
               <option value="S13">Computer Science</option>
               <option value="S01" >Statistics</option>
           </select>
       </p>

       <?php echo $request->getProperty('year_error') ?? ''; ?>
       <p>
        <label for="yos">Year of study: </label>
        <select name="year" id="yos">
               <option label=""></option>
               <option value="1">Y1</option>
               <option value="2">Y2</option>
               <option value="3">Y3</option>
               <option value="4">Y4</option>
               <option value="5">Y5</option>
        </select>
       </p>

       <?php echo $request->getProperty('semester_error') ?? ''; ?>
       <p>
        <label for="sos">Semester of study: </label>
        <select name="semester" id="sos">
               <option label=""></option>
               <option value="1">S1</option>
               <option value="2">S2</option>

        </select>
        </p>
       <input type="hidden" name="action" value="AddStudent">
        <p class="submit_p"><input type="submit" value="Add Student" class="submit_b"></p>
        </form>
        </div>
    </div>
 <?php
include_once('templates/admin_footer.php')
 ?>