<?php
include_once('templates/admin_header.php');
?>
<div class="">
        <div class="form login admin">
    
        <p><h1>Add Lecturer</h1></p>
        <form action="" method="post">

        
       <p> <label for="email">Email:</label><input type="text" id="email" class="input" name="email"></p>
       <p><label for="id">Lecturer ID: </label><input type="text" id="id" class="input" name="id"></p>
       <p><label for="password">Password: </label><input type="password" id="password" class="input" name="password"></p>
       <p><label for="first_name">First Name: </label><input type="text" id="first_name" class="input" name="first_name"></p>
       <p><label for="middle_name">Middle Name: </label><input type="text" id="middle_name" class="input" name="middle_name"></p>
       <p>
           <label for="course">Course: </label>
           <select name="course" id="course">
           <!--A php loop will be placed here for all the courses-->
               <option value="computer">Computer Science</option>
               <option value="statistics">Statistics</option>
           </select>
       </p>
       <input type="hidden" action="AddLecturer">
        <p class="submit_p"><input type="submit" value="Add Lecturer" class="submit_b"></p>
    </form>
        </div>
    </div>
 <?php
include_once('templates/admin_footer.php')
 ?>