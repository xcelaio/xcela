<?php

    namespace System;
    !_?die:_;
    
    /**
        xcela.io
        MySQL

        @description
            iDB (DB Abstraction Interface) implementation for MySQL (mysql_*)
            legacy PHP driver. This is a static class that is part of a multiton
            model. Do not instantiate directly; use the Database class' Instance()
            method instead.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    class MySQL implements iDB
    {
        private static $link;
        private static $database;
        private static $query;
        
        public static function Link($h, $u, $p, $d, $f)
        {
            self :: $link = mysql_connect($h, $u, $p, true, $f);
            self :: $database = mysql_select_db($d, self :: $link);
        }
        
        public static function Unlink()
        {
            if (self :: $link)
                mysql_close(self :: $link);
        }
        
        public static function Query($str, $preserve = false)
        {
            $str = preg_split('/^\s*ALSO\s*$/smx', $str);
            
            foreach($str as $query)
            {
                $q = mysql_query($query);
                
                if ($q === false)
                    throw new SystemException(mysql_error() . ' using query ' . $query);
            }
            
            if ($preserve)
                self :: $query = $q;
            
            return $q;
        }
        
        public static function Cascade($str)
        {
            $str = preg_split('/^\s*ALSO\s*$/smx', $str);
            
            $id = array();
            foreach($str as $query)
            {
                for ($i = 0; $i < count($id); $i++)
                    $query = str_replace('INSERTID[' . $i . ']', $id[$i], $query);
                
                $q = mysql_query($query);
                $id[] = mysql_insert_id();
                
                if ($q === false)
                    throw new SystemException(mysql_error());
            }
            
            return $q;
        }
        
        public static function Fetch($query, $fromcache = false)
        {
            return mysql_fetch_assoc(($fromcache === true ? self :: $query : $query));
        }
        
        public static function Row($query, $fromcache = false)
        {
            return mysql_fetch_row(($fromcache === true ? self :: $query : $query));
        }
        
        public static function Count($query, $fromcache = false)
        {
            return mysql_num_rows(($fromcache === true ? self :: $query : $query));
        }
        
        public static function ID()
        {
            return mysql_insert_id();
        }
        
    }

?>