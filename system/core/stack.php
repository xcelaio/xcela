<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Stack

        @description
            The stack class allows the router to handle more than one process
            successively; adding to the stack will cause the router to run another 
            process once the active processes are done. The stack is ordered and 
            processed from top to bottom.
            
            This is a static class. Do not instantiate or call any magic functions.
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    class Stack
    {
        private static $stack;
        private static $ptr = 0;
        
        /**
            Gets the position of the stack pointer
            
            @return     [int] Position of the stack pointer
        */
        public static function Pointer()
        {
            return self :: $ptr;
        }
        
        /**
            Retrieves an item from the stack.
            
            @param      [int] item ID to retrieve
            @return     [string] the stack entry at the given position [boolean:false] if the stack didn't contain the provided id
        */
        public static function Get($id)
        {
            if (count(self :: $stack) > $id)
                return self :: $stack[$id];
            
            return false;
        }
        
        /**
            Retrieves the item on top of the stack.
            
            @return     [string] the stack entry at the top of the stack.
        */
        public static function Top()
        {
            return self :: Get(self :: $ptr);
        }

        /**
            Pops the item on top of the stack and pulls the entire stack up
            one position. This is normally called at the end of a process cycle to
            prepare for the next process in the stack.
            
            @return [boolean] true indicates success. false indicates failure.
        */
        public static function Pop()
        {
            if (count(self :: $stack) > self :: $ptr)
            {
                self :: $ptr ++;
                return true;
            }
            return false;
        }
        
        /**
            Pushes a process (or a list of processes) into the stack. Processes
            are inserted at the end of the stack in the order they were received.
            
            @param      [string] single entity to push into the stack [array:string] multiple entities to push into the stack
            @return     [boolean] true on success. false on failure.
        */
        public static function Push($str)
        {
            $str = str_replace('.', '/', trim($str));
            
            if (!is_array(self :: $stack))
                self :: $stack = array();
            
            if (!is_array($str))
                $str = array($str);
            
            foreach ($str as $val)
            {
                if (self :: Assert($val))
                    self :: $stack[] = $val;
                else
                    throw new SystemException(err_stack);
            }
            
            return true;
        }
        
        /**
            Ensures the stack is not looping or overflowing. This is called before
            a push is completed and prevents the last item to push in the stack
            from being the same as the previous one (loop condition) and/or
            for the stack to exceed the stack limit (overflow condition).
            
            @param  [string] The stack item to be pushed
            @return [boolean] true on success, false on failure.
        */
        public static function Assert($str)
        {
            $count = count(self :: $stack);
            
            if (is_array(self :: $stack) and $count > 0)
            {
                if (self :: $stack[$count - 1] == $str)
                    return false;
                
                if ($count > max_stack)
                    return false;
            }
            
            return true;
        }
        
        /**
            Returns the path of the resource that's on top of the stack.
            
            @return [string] Resource on top of the stack
        */
        public static function Path()
        {
            return str_replace('/', '.', self :: Get(0));
        }

        /**
            Counts the items still present in the stack
            
            @return [int] Total items in the stack
        */
        public static function Count()
        {
            if (!is_array(self :: $stack))
                return 0;
            
            return count(self :: $stack);
        }
        
        /**
            Counts the stacked items left to process ahead
            
            @return [int] Stacked items left to process
        */
        public static function Ahead()
        {
            if (!is_array(self :: $stack))
                return 0;
            
            return count(self :: $stack) - self :: $ptr;
        }
    }

?>