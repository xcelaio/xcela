<?php
    
    namespace System;
    !_?die:_;

    /**
        xcela.io
        XSS

        @description
            This file filters any client input and parses the contents of any
            data that was sent to replace potentially harmful entities to 
            their HTML entity correspondance. 

            Note that because of this safety barrier, encrypted data must
            be formated as base64 entities in order to prevent accidental 
            corruption of a binary-encoded encrypted data package.

            The sanitizer is triggered automatically at the time this file
            is included. It runs recursively through all data inputs. Note
            that recursivity is limited by the depth setting; any data set
            that resides deeper than the recursivity limit will be destroyed.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    if (isset($_REQUEST) and is_array($_REQUEST))
        $_REQUEST = Sanitize($_REQUEST);
        
    function Sanitize($a, $d = 0)
    {
        // Entities to clean off the requests
        $e = array
        (
            '"'         => '&quot;', 
            '\''        => '&#039;', 
            '<'         => '&lt;', 
            '>'         => '&gt;', 
            '\\'        => '&#092;'
        );

        // Scan through the array (key = value)
        foreach ($a as $k => $v)
        {
            // Convert recursively up to max_depth
            if (is_array($v))
                $a[$k] = ($d < max_depth ? Sanitize($v, $d + 1) : null);
            
            else
                $a[$k] = trim(strtr(stripslashes($v), $e));
        }

        return $a;
    }

?>