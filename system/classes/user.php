<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        User

        @description
            Placeholder class for user handling
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    class User
    {
        /*
            ID
            Returns the current user ID
        */
        public static function ID()
        {
            if (isset($_SESSION[session_datakey]) and is_array($_SESSION[session_datakey]) and isset($_SESSION[session_datakey]['id']))
            {
                $id = $_SESSION[session_datakey]['id'];

                // This is a security measure to ensure nobody spoofs as user zero which often evaluates as false
                // if the code is not tested enough.
                if ($id === 0 or $id === null or $id === false or !is_numeric($id))
                    return false;

                return $id;
            }
            return false;
        }
        
        public static function Commit()
        {
            session_write_close();
            session_start();
        }
        
        public static function Clear()
        {
            $_SESSION[session_datakey] = null;
            self :: Commit();
            return true;
        }
        
        public static function Set($var, $val)
        {
            $_SESSION[session_datakey][$var] = $val;
            self :: Commit();
        }
        
        public static function Get($var)
        {
            if (isset($_SESSION[session_datakey]) and isset($_SESSION[session_datakey][$var]))
                return $_SESSION[session_datakey][$var];
        }
        
    }

?>