<?php
include_once('templates/admin_header.php');
?>
<div class="">
        <div class="form login admin">
        <p><h1>Update Full Session</h1></p>

       </p>
       <p><label for="cohort_year">Cohort Year:</label>
       <input type="text" id="cohort_year" name="cohort_year" class="input">
       </p>  




       <input type="hidden" action="UpdateSession">
        <p class="submit_p"><input type="submit" value="Update Session" class="submit_b"></p>
        <p>Note you can <em>Batch Link</em> units to courses <a href="#">Learn more</a></p>
        </div>
    </div>


 <?php
include_once('templates/admin_footer.php')
 ?>