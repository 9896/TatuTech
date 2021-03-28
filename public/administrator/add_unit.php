<?php
include_once('templates/admin_header.php');
?>
<div class="">
        <div class="form login admin">
        <p><h1>Add Unit</h1></p>
    <form action="" method="post">
    <?php echo $request->getProperty('success') ?? ''; ?>

       <?php echo $request->getProperty('id_error') ?? ''; ?>
       <p><label for="id">Unit ID: </label><input  id="id" class="input" name="id"
       value="<?php echo $_POST['id'] ?? ''; ?>"></p>

       <?php echo $request->getProperty('name_error') ?? ''; ?>
       <p><label for="name">Unit Name: </label><input id="name" class="input" name="name"
       value="<?php echo $_POST['name'] ?? ''; ?>"></p>

       <input type="hidden" name="action" value="AddUnit">
        <p class="submit_p"><input type="submit" value="Add Unit" class="submit_b"></p>
    </form>
        <p>Note that <em>Batch insertion</em> is supported <a href="#">learn more</a></p>
        </div>
    </div>

 <?php
include_once('templates/admin_footer.php')
 ?>