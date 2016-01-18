<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Index

        @description
            This is the core of *xcela*, it is instantiated from the index file
            and is responsible for conducting all processes.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    /**
        Core class
        
        @description
            This class is instantiated by the index and will stay active
            until the end of the execution process.
        @/
    */
    class Core
    {
        private $buffer;

        public function __construct()
        {
            // Enforce time limit, ignore aborts
            set_time_limit(2);
            ignore_user_abort();

            // Load constants
            require_once('system/core/constants.php');

            // Set error reporting (debuging mode)
            if (debug)
                error_reporting(E_ALL ^ E_DEPRECATED);
            
            // Load security
            require_once('system/core/security.php');

            // Load dependencies
            require_once('system/core/dependencies.php');

            // Session handling
            date_default_timezone_set(local_timezone);
            session_start();

            // Initialize the stack (stack routing)
            Stack :: Push(((isset($_REQUEST[route_key]) and trim($_REQUEST[route_key]) != '') ? $_REQUEST[route_key] : route_home));
            
            // Initialize buffering
            ob_start();
            {
                // Cycle the processes stack, run all the processors sucessively until the stack is empty.
                while (Stack :: Ahead() > 0)
                {
                    // Pre-init / re-init 'found' flag (in case the stack had multiple items)
                    $found = false;
                    
                    // Catch anything that might happen
                    try
                    {
                        foreach (route_repos as $rep => $types)
                        {
                            if (!is_array($types))
                                $types = array($types);
                            
                            foreach ($types as $t)
                            {
                                $p = $rep . Stack :: Top() . $t;
                                
                                if (is_file($p))
                                {
                                    $found = true;

                                    // Update the output sequencer
                                    Output :: Path(Stack :: Path());

                                    if (!include($p))
                                        throw new SystemException(sprintf(err_include500, Stack :: Top()));

                                    break 2;
                                }
                            }
                        }
                        
                        if (!$found)
                            throw new SystemException(sprintf(err_include404, Stack :: Top()));
                        
                        // Processor completed; pop the stack
                        Stack :: Pop();
                    }
                    
                    catch (Exception $e)
                    {
                        throw new SystemException($e);
                    }
                }

                $this -> buffer = ob_get_contents();
                ob_end_clean();
            }
            
            // Pass the buffer to the output handler for final render
            Output :: Flush($this -> buffer);
            exit(1);
        }
    }

?>