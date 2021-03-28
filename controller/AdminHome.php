<?php
namespace controller\AdminHome;

use controller\Command\Command as Command;
use controller\Request\Request as Request;

require_once('Request.php');
require_once('Command.php');

/**
 * This is the default command class which auto matically serves up the AdminHome page
 * Obviously it has no use for the Request Object but it must implement it as it is the only exception
 */
class AdminHome extends Command{

    function execute(Request $request){
        include_once('admin_home.php');
    }
}

?>