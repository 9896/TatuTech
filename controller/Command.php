<?php
namespace controller\Command;

use controller\Request\Request as Request;

/**
 * The command base class does nothing more but to lie down the abstract execute class that all its 
 * children must execut
 * Note that the command class must have as little logic, itself mostly delegating to other classes and 
 * finally delivering the correct view
 * 
 * @package command
 */
abstract class Command{
    /**
     * This method is declared final to ensure no subclass overides it and adds a parameter. It is
     * utilized in the commandResolver class by the ReflectionObject {@link CommandResolver::getCommand()}
     * @param null
     * @return command
     */
    final function __construct(){}
    /**
     * The execute method delegates functions to other classes suited for the operation while it remains
     * clean and only delivering the views after analysis
     * @param $request Request. {@link Request}
     * @return view.
     */
    abstract function execute(Request $request);
}
?>