<?php

    namespace System;
    !_?die:_;

    /**
        xcela.io
        Cryptography

        @description
            Cryptography class
        @/

        @namespace  System
        @author     Claude Desjardins <evilpea3@gmail.com>
        @copyright  2015-2016 xcela.io
        @license    http://doc.xcela.io/license/ MIT
    */

    class Cryptography
    {
        private $key;
        private $iv;
        
        /**
         * Sets IV to be used for decryption
         * @param string $str IV to use for decryption (base64 encoded)
         */
        public function SetIV($str)
        {
            $this -> iv = base64_decode($str);
        }
        
        /**
         * Returns the IV computed during encryption.
         * @return string IV value
         */
        public function GetIV()
        {
            return base64_encode($this -> iv);
        }
        
        /**
         * Sets encryption key to be used for decryption. Key must be 16, 24 or 32 bytes long.
         * @param string $k IV Key
         */
        public function SetKey($k)
        {
            // Key need to be 16, 24 or 32 bytes long.
            if (!in_array(strlen($k), array(16, 24, 32)))
                throw new SystemException(err_key);

            else
                $this -> key = $k;
        }
        
        /**
         * Encrypts input data using RIGNDAEL 256
         * @param  string $str String to be encrypted
         * @return string Encrypted string
         */
        public function Encrypt($str)
        {
            $this -> iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_RAND);
            return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this -> key, $str, MCRYPT_MODE_CBC, $this -> iv));
        }

        /**
         * Decrypts input data using RIGNDAEL 256
         * @param  string $str Data to decrypt (base64 encoded)
         * @return string Decrypted string
         */
        public function Decrypt($str)
        {
            return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this -> key, base64_decode($str), MCRYPT_MODE_CBC, $this -> iv), "\0");
        }
    }

?>