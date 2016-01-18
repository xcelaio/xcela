<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Dependencies Requirements Handler

        @description
            This file is responsible for including all the files required for the system to run.
            It includes all the system files specified in the dependencies list; included files
            are expected to reside in the system's core folder. This is inline code and is 
            launched automatically whenever this file is included by the core.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    foreach (dependencies as $d)
    {
        $d = strtolower(trim($d));
        
        if (is_file(path_core . $d))
        {
            if (!require_once(path_core . $d))
                throw new SystemException(sprintf(err_include500, $d));
        }
        else
            throw new SystemException(sprintf(err_include404, $d));
    }


    /*
        This function has global scope and is called whenever a "new" keyword is 
        used while the targetted class was not already declared in the system. This avoids
        having to manually include class files; instantiating classes is all that's required
        to include and instantiate the class.
        
        Classes files are expected to have the same name as the file they reside in 
        (case insensitive). Classes files should be in the system's classes folder.
    */
    function AutoLoader($c)
    {
        $c = strtolower(trim($c));
        
        // Strip off the namespace if present
        if (preg_match('/\\\\?([^\\\\]+)\z/i', $c, $r))
	       $c = $r[1];

        // Attempt to locate and include the class file
        if (is_file(path_classes . $c . ext_php))
        {
            if (!require_once(path_classes . $c . ext_php))
                throw new SystemException(sprintf(err_include500, $c));
        }
        
        else
            throw new SystemException(sprintf(err_include404, $c));
    }
    spl_autoload_register('\system\AutoLoader');

?>