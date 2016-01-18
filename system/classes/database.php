<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Database

        @description
            Database class. This class is an implementation of the
            database interface (iDB), used as part of the DB abstraction
            model. 
            
            This class is a multiton. Do not instantiate; access the Instance()
            method instead.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */


    class Database implements iDB
    {
        private static $instance;                                   // Me, Myself, and I
        private static $database;                                   // Reference to the database driver
        
        /*
            Instance
            Multiton instance management; this returns the required 
            instance of this class and/or creates one if required.
            This will also trigger the initialization procedure upon
            creating a new instance.
        */
        public static function Instance($db = null)
        {
            if ($db === null)
                $db = db_primary;
            
            $sig = $db['host'] . '/' . $db['database'];
            
            if (!isset(static :: $instance) or static :: $instance === null or !is_array(static :: $instance) or !isset(static :: $instance[$sig]))
            {
                static :: $instance[$sig] = new static();
                static :: $database = null;

                self :: Initialize($db['interface'], $db['flag'], $db['host'], $db['user'], $db['password'], $db['database']);
            }
            
            return static :: $instance[$sig];
        }
        
        /*
            Dead-End (!)
            These overrides prevent construction, cloning and waking up of
            the multiton class.
        */
        protected   function    __construct()   {}
        private     function    __clone()       {}
        private     function    __wakeup()      {}
        
        /*
            Initialize
            This determines the database driver to use, instantiate and reference it
            then launches the driver's link method to establish database connection.
        */
        private static function Initialize($i, $f, $h, $u, $p, $d)
        {
            if (static :: $database === null)
            {
                // Database Interface Factory Model - Include and instantiate database interface class ($i)
                require_once path_classes . 'dbi/' . strtolower($i) . '.php';
                static :: $database = new $i();

                self :: Link($h, $u, $p, $d, $f);
            }
        }
        
        /*
            Link
            Establishes database connection
        */
        public static function Link($h, $u, $p, $d)
        {
            if (static :: $database !== null)
                return static :: $database -> Link($h, $u, $p, $d, $f);
            return false;
        }
        
        /*
            Unlink
            Closes the link with the database server
        */
        public static function Unlink()
        {
            if (static :: $database !== null)
                return static :: $database -> Unlink();
            return false;
        }
        
        /*
            Query
            Queries the database server. $preserve is a keepalive
            variable; if you want to use the same query results
            without running the query several times, you may 
            set a keepalive and use the "fromcache" variables in
            Fetch, Row and Count. "Str" can be an array of multiple
            queries; although, only the last query result will be returned.
        */
        public static function Query($str, $preserve = false)
        {
            if (static :: $database !== null)
                return static :: $database -> Query($str, $preserve);
            return false;
        }
        
        /*
            Cascade
            Allows running multiple inserts in cascade transporting
            the last insertion ID back onto the next query.
        */
        public static function Cascade($str)
        {
            if (static :: $database !== null)
                return static :: $database -> Cascade($str);
            return false;
        }
        
        /* 
            Fetch
            Returns an associative array of the results at the current
            row position. This increments the row pointer.
        */
        public static function Fetch($query, $fromcache = false)
        {
            if (static :: $database !== null)
                return static :: $database -> Fetch($query, $fromcache);
            return false;
        }
        
        /*
            Row
            Returns an associative array of the results at the current position
            assuming only one row was returned by the query.
        */
        public static function Row($query, $fromcache = false)
        {
            if (static :: $database !== null)
                return static :: $database -> Row($query, $fromcache);
            return false;
        }
        
        /*
            Count
            Counts the results provided by a given query
        */
        public static function Count($query, $fromcache = false)
        {
            if (static :: $database !== null)
                return static :: $database -> Count($query, $fromcache);
            return false;
        }
        
        /*
            ID
            This allows retrieving the last inserted index
        */
        public static function ID()
        {
            if (static :: $database !== null)
                return static :: $database -> ID();
        }
        
    }

    /*
        Database Abstraction Interface
    */
    interface iDB
    {
        public static function Link($h, $u, $p, $d, $f);
        public static function Unlink();
        public static function Query($str, $preserve);
        public static function Cascade($str);
        public static function Fetch($query, $fromcache);
        public static function Row($query, $fromcache);
        public static function Count($query, $fromcache);
        public static function ID();
    }

?>