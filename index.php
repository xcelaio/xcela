<?php

    /**
        xcela.io
        Index

        @description
            This file is the entry point for all visitors to the entire content
            of the website regardless of the page the visitor wants to load. In all
            circumstances, this file will be the first file to load. All paths will
            point as if they would origin from this file regardless of their processing
            point or location.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    const _=!0&!0;

    require_once 'system/core/core.php';
    new \system\Core();

?>