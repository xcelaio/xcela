<?php

    /**
        xcela.io
        Security 

        @description
            This inline file is included at core level and is used to establish page-dependant
            security, access control and account heritage. It is still a work in progress :}
        @/

        @todo       Finish this :}
        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */


    //use __ as security;

    if (!defined('_') or _ != 1)
        die ('System Integrity Error');

    const u = 0x100;
    const r = 0x200;
    const w = 0x400;

    function __(...$arg)    { return Security :: Check($arg); }
    function read()         { return Security :: $read;       }
    function write()        { return Security :: $write;      }

    class Security
    {
        public static $read = true;
        public static $write = true;
        
        public static function Check($arg)
        {
            $rm = 0xF00;
            $lm = 0xFF;
            
            $l = 10;
            
            foreach($arg as $a)
            {
                // require = ($a & $rm);
                // minlvl = ($a & $lm);
            
                if (($a & $rm) & u and !isset($_SESSION[\system\session_datakey][0]['id']))
                    throw new Exception('Access Error');
            
                self :: $read = (($a & $rm) & r ? $l >= ($a & $lm) : self :: $read);
                self :: $write = (($a & $rm) & w ? $l >= ($a & $lm) : self :: $write);
            }
            
            return true;
        }
    }

?>