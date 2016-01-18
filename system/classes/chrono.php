<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Chrono

        @description
            Gizmo chronometer class used to test execution timings.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    class Chrono
    {
        private $start;
        private $stop;
        
        public function Start()
        {
            $this -> start = microtime(true);
        }
        
        public function Stop()
        {
            $this -> stop = microtime(true);
        }
        
        public function GetResult()
        {
            return $this -> stop - $this -> start;
        }
    }

?>