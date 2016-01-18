<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Output Handler Class
        
        @description
            This class handles all outputs including chroming and headers. It forces outputs
            to occur in a given sequence to allow headers out prior to letting any contents out.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */


    class Output
    {
        private static $mime;
        private static $upload;
        private static $filename;
        private static $chrome;
        private static $base;
        private static $themebase;
        private static $path;

        public static function Initialize()
        {
            self :: $mime = mime_html;
            
            // Chrome handling
            self :: $chrome = (!isset($_REQUEST['chrome']) or (isset($_REQUEST['chrome']) and $_REQUEST['chrome']));
            // Setup variables
            self :: $base = tpl_base;
        }
        
        public static function Mime($mime)
        {
            self :: $mime = $mime;
        }
        
        public static function Upload($bool, $filename)
        {
            self :: $upload = $bool;
            self :: $filename = $filename;
        }
        
        public static function Chrome($bool)
        {
            self :: $chrome = $bool;
        }
        
        public static function Base($str)
        {
            self :: $base = $str;
        }
        
        public static function Path($path)
        {
            self :: $path = $path;
        }
        
        public static function Flush($buffer)
        {
            // Output default headers
            foreach (header_defaults as $header)
                header ($header);

            // Output the mime type
            if (defined(mime_assoc[self :: $mime]))
                header(mime_assoc[self :: $mime]);

            // Handle file upload (towards the client)
            if (self :: $upload === true)
                header(sprintf(header_upload, self :: $filename));
            
            // Prepare the URL string
            $url = '?' . route_key . '=' . self :: $path;
            foreach ($_GET as $var => $val)
                $url .= ($var != route_key ? '&' . $var . '=' . $val : '');
            
            // Theme wrapping
            $t = new Template((self :: $chrome ? self :: $base : ''));
            if (!self :: $chrome)
                $t -> Override('@!sys.buffer;');

            $t -> Affect(array(
                'sys.buffer'    => $buffer,
                'sys.url'       => $url,
                'sys.rand'      => uniqid(time()),
                'sys.time'      => time()
            ));

            $t -> Flush();

        }
    }

    // Launch the initializer
    Output :: Initialize();

?>