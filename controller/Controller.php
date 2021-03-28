<?php

namespace controller\Controller;

use controller\Request\Request as Request;
use controller\CommandResolver\CommandResolver as CommandResolver;
use controller\Command\Command as Command;

require_once('Request.php');
require_once('CommandResolver.php');
require_once('Command.php');

/**
 * This class initializes the system thus fetching and ensuring all relevant vairables are set and fetched
 * It also MOSTLY handled routing operation offering one stop terminal for request handling and dissemination
 * At initialization it works with the following classes
 * @package command
 * @see Request, CommandResolver and Command classes.
 */
class Controller{
    /**
     * Declared private to ensure its not called or instantiated outside the class
     * @param null
     * @return void
     */
    private function __construct(){}
    /**
     * This method will be run in the index file at initialization and after every request
     * Where caching is possible it shall be applied.Its static nature further simplifies initilization
     * and its execution
     * Its main purpose is to call init which is truly responsible of setting the ball in motion
     * @param null
     * @return void
     */
    static function run(){
        $c = new self();
        $c->init();
    }
    /**
     * Init does the actuall initialization fetching and instantiation all relevant players
     * @param null
     * @return void
     */
    function init(){
        $request = Request::getInstance();
        //$request->setProperty('action', 'show_AddStudent');
        $cmd = CommandResolver::getCommand($request);
        //echo get_class($cmd);
        $cmd->execute($request);
        $action = $request->getProperty('action');
        //echo $action;
    }
}

//Controller::run();
?>