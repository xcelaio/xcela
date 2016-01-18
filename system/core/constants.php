<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Constants

        @description
            All system / core constants (configurations) reside in this
            file to ease system configurations. 
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    // Debugging
    const debug             = true;

    // Localization
    const local_timezone    = 'America/Los_Angeles';                                                            // Timezone setting
    const local_currencysym = '$';                                                                              // Currency symbol
    const local_decimal     = '.';                                                                              // Decimal separator
    const local_thousand    = ',';                                                                              // Thousand separator
    const local_decplaces   = 2;                                                                                // Decimal places

    // Dependencies
    const dependencies      = array                                                                             // Dependencies array; those will automatically be included at core level
    (
        'xss.php',
        'exceptions.php',
        'stack.php',
        'output.php'
    );

    // Files, Paths, Extensions
    const path_core         = 'system/core/';                                                                   // Root-dependant path to core files
    const path_classes      = 'system/classes/';                                                                // Root-dependant path to classes files
    const path_procs        = 'public/proc/';
    const path_templates    = 'public/tpl/';

    const ext_php           = '.php';                                                                           // Generic php file extension
    const ext_tpl           = '.tpl';                                                                           // Generic tpl file extension
    const ext_xml           = '.xml';                                                                           // Generic xml file extension
    const ext_txt           = '.txt';                                                                           // Generic txt file extension

    // Stack and Recursion
    const max_depth         = 10;                                                                               // Depth limit for recursion processes
    const max_stack         = 10;                                                                               // Size limit for the execution stack

    // Output handling, Headers, Mimes
    const tpl_base          = 'base.tpl';

    const header_upload     = 'Content-Disposition: attachment; Filename="%s"';
    const header_defaults   = array
    (
        'Cache-Control: no-store, no-cache, must-revalidate, max-age=0',
        'Cache-Control: post-check=0, pre-check=0',
        'Pragma: no-cache'
    );

    const mime_html         = 'html';
    const mime_xml          = 'xml';
    const mime_txt          = 'txt';
    const mime_assoc        = array
    (
        mime_html           => 'Content-Type: text/html',
        mime_xml            => 'Content-Type: text/xml',
        mime_txt            => 'Content-Type: text/plain'
    );

    // Routing
    const route_key         = 'k';                                                                              // Routing key
    const route_home        = 'home';                                                                           // Path home
    const route_repos       = array                                                                             // Route scanning path(s)
    (
        path_procs          => ext_php,
        path_templates      => ext_tpl
    );

    // Database
    const db_primary        = array
    (
        'interface' =>      'MySQL',
        'flag'      =>      65536,
        'host'      =>      'localhost',
        'user'      =>      'myusername',
        'password'  =>      'mypassword',
        'database'  =>      'mydatabase'
    );

    // Error Messages
    const err_include404    = 'Unable to locate required file %s';                                              // Error to display if a file could not be found
    const err_include500    = 'Fatal error while attempting to load file %s';                                   // Error to display if an inclusion causes a crash
    const err_fatal         = 'Fatal error while attempting to run the requested process.';                     // Error to display on a system exception when 'debug' is off
    const err_template      = 'Unable to locate template file %s';                                              // Error to display when a template file can not be found
    const err_exception     = 'Exception at %s: [%s]: %s';                                                      // Error to display when a system exception is thrown
    const err_key           = 'Cryptography key error. Key must be 16, 24 or 32 bytes long.';                   // Error to display when a crypto key does not meet requirements
    const err_primarydb     = 'Failed attempt to instantiate primary database without credentials.';            // Error to display when instantiating the primary db without proper login
    const err_stack         = 'Stack flow error. The stack is either looping or overflowing.';                  // Error to display when the stack fails to assert a push request

    // Generic constants
    const crlf              = "\r\n";                                                                           // Carriage return + LineFeed (this requires double quotes in this case)

?>