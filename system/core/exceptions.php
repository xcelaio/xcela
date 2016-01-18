<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Exceptions/Error Handler

        @description
            The exception handler is responsible for intercepting PHP's errors and
            exceptions and provide better handling (such as debugging).
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */


    
    class SystemException extends \Exception
    {
        public function __construct($m = null, $c = 0, Exception $p = null)
        {
            parent :: __construct($m, $c, $p);
        }
        
        public function __toString()
        {
            if (debug)
                self :: OutputException();
            else
                echo err_fatal;

            return "";
        }
        
        public function OutputException()
        {
            echo '<h1>Error.</h1>';
            echo '<p>Stack Trace</p>';
            
            // Show base
            $msg = sprintf(err_exception, __CLASS__, $this -> code, "<i>". $this -> message . '</i> attempting to run process ' . Stack :: Top());
            $basearray = array(
                'class' => __CLASS__,
                'code' => $this -> code,
                'message' => $this -> message,
                'file' => $this -> file,
                'line' => $this -> line
            );
            self :: Render('Error Base', $msg, $basearray);
            
            // Show codelines
            $codelines = self :: GetCodeLines(5);
            self :: Render('Source', 'From ' . $this -> file . ' around line ' . $this -> line, implode($codelines, ''));

            // Show backtrace
            $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
            if (count($trace) > 1)
                unset($trace[0]);
            
            self :: Render('Backtrace', null, $trace);
            
            // Show stack
            $stack = array();
            for ($i = 0; $i < Stack :: Count(); $i++)
                $stack[$i] = Stack :: Get($i);

            $msg = 'Stack pointer at position #' . Stack :: Pointer() . ' (' . Stack :: Top() .')';
            self :: Render('Stack', $msg, $stack);
            
            // Show requests
            self :: Render('Requests', null, $_REQUEST);
        }
        
        public function Render($title, $text, $data)
        {
            echo '<h2>' . $title . '</h2>';
            if ($text != null)
                echo '<p>' . $text . '</p>';
            
            echo '<pre>';
            
            if (is_array($data))
                print_r($data);
            else 
                echo $data;
            
            echo '</pre>';
        }

        public function GetCodeLines($pad)
        {
            $line = $this -> line - 1;
            $lines = file($this -> file);
            
            $start = $line - $pad;
            if ($start < 0)
                $start = 0;
            
            $stop = $line + $pad;
            if ($stop >= count($lines))
                $stop = count($lines) - 1;
            
            $arr = array();
            for ($i = $start; $i < $stop; $i++)
            {
                if ($i == $line)
                    $arr[] = '<strong>' . $lines[$i] . '</strong>';
                else
                    $arr[] = $lines[$i];
            }
            return $arr;
        }
        
    }

    class ExceptionClass
    {
        public function Register()
        {
            set_error_handler(array($this, 'handleError'));
            set_exception_handler(array($this, 'handleException'));
            register_shutdown_function(array($this, 'handleShutdown'));
        }

        public function handleException($exception)
        {
        }


        public function handleError($level, $message, $file = null, $line = null)
        {
        }

        public function handleShutdown()
        {
        }
    }

?>