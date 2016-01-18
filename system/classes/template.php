<?php

    namespace System;
    !_?die:_;
    
    /**
        xcela.io
        Template

        @description
            The template class allows HTML manipulations from PHP codes. 
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    class Template
    {
        private $buffer;
        private $loop;
        
        /*
            Constructor
            With a provided parameter, this will preload the specified file.
        */
        public function __construct($f = null)
        {
            if ($f != null)
                $this -> Load($f);
        }
        
        /*
            Loader
            This method loads the contents of a file into the buffer
        */
        public function Load($f, $path = path_templates)
        {
            // Attempt to load the file
            if (!is_null($f) and is_file($path . $f))
                $this -> buffer = file_get_contents($path . $f);
            
            else
                throw new SystemException(sprintf(err_template, $path . $f));    
        }

        /*
            Buffer override
        */
        public function Override($str)
        {
            $this -> buffer = $str;
        }
        
        /*
            Fetches a content block
            This method scans the buffer for a block with the given name then locates it's closing tag
            considering block nesting. Note that if the template contains the same block multiple times, only
            the first block that was found will be returned. Search is case insensitive. This function
            returns the located string including the matched tags.
            
            This function can now accept a buffer parameter to look into a specific buffer. If none specified,
            it'll roll back to the class' buffer instead.
        */
        public function FetchBlock($name, $buffer = null)
        {
            if ($buffer == null)
                $buffer = $this -> buffer;
            
            // This locates the block (by name) and captures it up to the last occurence of an @end character.
            // Anything before the block entity is voided, capture will last up to the last @end in the buffer.
            if (preg_match('/@block[\s]*\([\s]*\'' . $name . '\'[\s]*\).*@end/si', $buffer, $block))
            {
                // Locate the end of the block and return the result
                return $this -> LocateEnd($block[0]);
            }
            
            return false;
        }
        
        private function LocateEnd($str)
        {
            $ptr = 0;
            $nests = 0;
            
            $depth = 50;
            while(($depth--) > 0)
            {
                $pos = strpos($str, '@', $ptr);
                
                // @ was not found during a nesting search, the template is probably malformed. Abandon and return what we got
                if ($pos === false)
                    return $str;
                
                // Block located; we're gonna need an end for that. Increment the nests counter.
                if (strtolower(substr($str, $pos, 6)) == '@block')
                    $nests++;
                
                // Found an end, decrement the nests counter.
                else if (strtolower(substr($str, $pos, 4)) == '@end')
                    $nests--;

                // No more nests in the stack; we're done looking.
                if ($nests == 0)
                    return substr($str, 0, $pos + 4);
                
                // Prepare the pointer for the next pass
                $ptr = $pos + 1;
            }
            
            // We've overrun the depth limit, just return the string as-is.
            return $str;
        }
        
        
        /*
            Toggles a content block on or off
            $bool: true = sets the content block as visible, false = removes the content block
        */
        public function Toggle($name, $val)
        {
            while(($block = $this -> FetchBlock($name)) !== false)
            {
                // Boolean case; turn the entire block on or off
                if ($val === true or $val === false)
                    $this -> buffer = str_replace($block, ($val ? $this -> Clean($block) : null), $this -> buffer);

                // Name case; fetch the inner block and replace the outer block with it
                else
                {
                    $innerblock = $this -> FetchBlock($val, $block);
                    $this -> buffer = str_replace($block, $this -> Clean($innerblock), $this -> buffer);
                }
            }
        }
        
        /*
            Alias to remove blocks
            This is identical to calling ToggleBlock false
        */
        public function Kill($name)
        {
            $this -> Toggle($name, false);
        }

        /*
            Affect
            Assigns variables to the template; ref can be an array var=>val or
            a variable name (in which case the value must be provided)
        */
        public function Affect($ref, $val = null, $reactor = null)
        {   
            // Converts ref to an array if it isn't one (use val for value and ref as reference)
            if (!is_array($ref))
                $ref = array($ref => $val);
            
            foreach ($ref as $var => $val)
            {
                // Hold the regexp in memory; we're using this twice
                //$reg = '/(@!' . $var . '(:("|\')(.*?)\g{3}))|(@!' . $var . '[[:>:]])/si';
                $reg = '/(@!' . $var . '(:("|\')(.*?)\g{3});)|(@!' . $var . ';)/si';
                
                if (preg_match($reg, $this -> buffer, $results))
                {
                    // Check if formatting parameters were passed
                    if (!empty($results[4]))
                        $val = $this -> Format($results[4], $val);
                    
                    // Check if a reactor was provided
                    if (!is_null($reactor))
                        $val = $this -> Reactor($reactor, $var, $val);

                    $this -> buffer = preg_replace($reg, $val, $this -> buffer);
                }
            }
        }
        
        /*
            Format
            Formats a string according to a format set
        */
        public function Format($format, $value, $type = null)
        {
            // Format was not provided, it should be in the string itself. Extract the first character of the string.
            if (is_null($type))
            {
                $type = substr($format, 0, 1);
                $format = substr($format, 1);
            }
            
            switch(strtolower($type))
            {
                // String
                default:
                case ('s'):
                    return sprintf($format, $value);
                break;
                    
                // Numeric
                case ('n'):
                    return number_format($value, $format);
                break;
                    
                // Monetary
                case ('m'):
                    // I have no idea why but removing the ' ' space below screws up the return ... To be checked
                    return (string) (local_currencysym . ' ' . number_format($value, local_decplaces, local_decimal, local_thousand));
                break;
                    
                // Date/Time
                case ('d'):
                case ('t'):
                    return date($format, $value);
                break;
                    
                // Lowercase
                case ('l'):
                    return strtolower($value);
                break;
                    
                // Uppercase
                case ('u'):
                    return strtoupper($value);
                break;
            }
        }
        
        /*
            Start Loop
            This locates the block to loop and buffers it to run looping blocks.
        */
        public function StartLoop($name)
        {
            // Load the block in memory
            $block = $this -> FetchBlock($name);
            
            $this -> loop = array
            (
                'name'          => $name,
                'source'        => $block,
                'iteration'     => $block,
                'buffer'        => ''
            );
        }
        
        /*
            This is similar to the Affect function with the exception that it affects
            the looped contents.
        */
        public function AffectLoop($ref, $val = null, $reactor = null)
        {
            /* 
                The contents here should be merged with "Affect" somehow to avoid having that same regular expression
                twice in this file. 
            */
            if (!isset($this -> loop))
                return;
            
            // Converts ref to an array if it isn't one (use val for value and ref as reference)
            if (!is_array($ref))
                $ref = array($ref => $val);
            
            foreach ($ref as $var => $val)
            {
                //$reg = '/(@!' . $var . '(:("|\')(.*?)\g{3}))|(@!' . $var . '[[:>:]])/si';
                $reg = '/(@!' . $var . '(:("|\')(.*?)\g{3});)|(@!' . $var . ';)/si';
                
                
                if (preg_match($reg, $this -> loop['iteration'], $results))
                {
                    // Check if formatting parameters were passed
                    if (!empty($results[4]))
                        $val = $this -> Format($results[4], $val);
                    
                    // Check if a reactor was provided
                    if ($reactor !== null)
                        $val = $this -> Reactor($reactor, $var, $val);

                    $this -> loop['iteration'] = preg_replace($reg, $val, $this -> loop['iteration']);
                }
            }
        }
        
        /*
            Iterates Loop
            This advances the loop, dumping it's iteration into the loop's buffer
        */
        public function IterateLoop()
        {
            if (!isset($this -> loop))
                return;
            
            $this -> loop['buffer'] .= $this -> Clean($this -> loop['iteration']);
            $this -> loop['iteration'] = $this -> loop['source'];
        }
        
        /*
            Commits the contents of the loop back to the main buffer, removing
            the original loop block from the main buffer replacing it with the
            processed loop buffer.
        */
        public function CommitLoop()
        {
            $this -> buffer = str_replace($this -> loop['source'], $this -> loop['buffer'], $this -> buffer);
            unset($this -> loop);
        }
        
        /*
            LoopBuffer
            Fetches the contents of a loop buffer
        */
        public function LoopBuffer($buffer)
        {
            return $this -> loop[$buffer];
        }
        
        /* 
            Override loop buffer
        */
        public function LoopOverride($buffer, $value)
        {
            $this -> loop[$buffer] = $value;
        }
        
        /* 
            Loop
            This is an alias to StartLoop, AffectLoop and IterateLoop all in one using a 
            3D array as the looping parameter.
        */
        public function Loop($name, $array)
        {
            $this -> StartLoop($name);
            foreach ($array as $a)
            {
                $this -> AffectLoop($a);
                $this -> IterateLoop();
            }
            $this -> CommitLoop();
        }
        
        public function DataRow($query, $prefix = null, $reactor = null)
        {
            $db = Database :: Instance();
            $sqlquery = $db :: Query($query);
            $row = $db :: Fetch($sqlquery);
         
            $a = array();
            if ($row !== false and is_array($row))
            {
                foreach ($row as $var => $val)
                {
                    if ($prefix !== null)
                        $var = $prefix . '.' . $var;
                    $a[$var] = $val;
                }
            }

            $this -> Affect($a, null, $reactor);
            
            return $row;
        }
        
        /*
            DataTable
            This uses an SQL instance to loop through a data table
        */
        public function DataTable($query, $block, $prefix = null, $toggles = null, $reactor = null)
        {
            $i = 0;
            $db = Database :: Instance();
            $sqlquery = $db :: Query($query);
            
            $this -> StartLoop($block);
            
            while ($row = $db :: Fetch($sqlquery))
            {
                $a = array();
                if ($row !== false and is_array($row))
                {
                    $i++;
                    
                    foreach ($row as $var => $val)
                    {
                        if ($prefix !== null)
                            $var = $prefix . '.' . $var;
                        $a[$var] = $val;
                    }
                }
                
                if ($toggles !== null)
                {
                    foreach ($toggles as $var => $val)
                    {
                        if (isset($row[$val]))
                        {
                            while(($block = $this -> FetchBlock($var, $this -> loop['iteration'])) !== false)
                            {
                                $innerblock = $this -> FetchBlock($row[$val], $block);
                                $this -> loop['iteration'] = str_replace($block, $this -> Clean($innerblock), $this -> loop['iteration']);
                            }
                        }
                        
                    }
                }
                
                $this -> AffectLoop($a, null, $reactor);
                $this -> IterateLoop();
            }
            
            $this -> CommitLoop();
            
            return array(
                'count' => $i,
                'last'  => $row
            );
        }

        /*
            DataHybrid
            This uses combinations of Row and Table type SQL queries to loop through
            data rows and tables.
        */
        public function DataHybrid($query, $reactor = null)
        {
            preg_match_all('/(?<query>.*?)(?:\R\s*AFFECTS\s*\(\s*(?<p1>[\w]+)\s*(?:,\s*(?<p2>[\w]+)\s*)?\)\s*(?:$|\R))/si', $query, $result, PREG_SET_ORDER);
            
            for ($i = 0; $i < count($result); $i++) 
            {
                // With P2 set, the query is a table type
                if (isset($result[$i]['p2']))
                    $this -> DataTable($result[$i]['query'], trim($result[$i]['p1']), (trim($result[$i]['p2']) != '' ? $result[$i]['p2'] : null), $reactor);

                // With P2 not set, the query is a row type
                else if (isset($result[$i]['p1']))
                    $this -> DataRow($result[$i]['query'], (trim($result[$i]['p1']) != '' ? $result[$i]['p1'] : null), $reactor);
            }
        }
        
        /*
            Clean
            Removes the @block('...') from the beginning of the block as well as the @end at
            the end of the block.
        */
        public function Clean($str)
        {
            return preg_replace('/\A@block[\s]*\([\s]*\'[^\']+\'[\s]*\)|@end\z/si', '', $str);
        }
        
        /*
            Reactor
            Handles reactor lambda-style scripts
        */
        private function Reactor($reactor, $key, $val)
        {
            // Reactors should affect the value of $INPUT; the reactor is first modified
            // to allow $INPUT be the value we received. The reactor is expected to contain
            // a return.
            return eval(str_replace('$KEY', '\'' . $key . '\'', str_replace('$INPUT', '\'' . $val . '\'', $reactor)));
        }
        
        /*
            Flush
            Flushes the template buffer back to the client
        */
        function Flush($return = false)
        {
            if ($return)
                return $this -> buffer;

            echo $this -> buffer;       
        }
        
    }

?>