<?php
/**
 * Utility function to encrypt - decrypt string using AES symmetric algorithm that is compatible with Crypto-JS
 *
 * @link https://code.google.com/p/crypto-js/ Crypto-JS
 * @author Gian Luca Dalla Torre <gianluca@gestionaleauto.com>
 *
 * @package CryptoParams
 */

namespace CryptoParams;

// Hex2Bin is not available on PHP 5.3
if ( !function_exists( 'hex2bin' ) ) {
	/**
	 *
	 * Transform an hexadecimal string into a binary array.
	 * 
	 * This function is defined here only because in PHP 5.3 it is not available. Otherwise the core function is used.
	 *
	 * @param string $str String expressed in hexadecimal format that needs to be converted
	 * @return string Binary string containing data
	 */
    function hex2bin( $str ) {
    	if (!ctype_xdigit($str)){
    		return FALSE;
    	}
        $sbin = "";
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i += 2 ) {
            $sbin .= pack( "H*", substr( $str, $i, 2 ) );
        }

        return $sbin;
    }
}

/**
 * 
 * Exception used to signal CryptoParams specific errors
 *
 * @author Gian Luca Dalla Torre <gianluca@gestionaleauto.com>
 * @link CryptoParams.CryptoParams.html CryptoParams
 */
class CryptoParamsException extends \Exception{}

/**
 * Utility class used to encrypt - decrypt string using AES symmetric algorithm that is compatible with Crypto-JS
 *
 * @author Gian Luca Dalla Torre <gianluca@gestionaleauto.com>
 *
 * @property string $key AES Key represented as hexadecimal string
 * @property string $iv Initialization vector represented as hexadecimal string
 */
class CryptoParams {

    /**
     * Internal representation (in binary format) of AES key
     *
     * @var string 
     * @ignore 
     */
	private $aesKey = null;

    /**
     * Internal representation (in binary format) of AES initialization vector
     *
     * @var string
     * @ignore     
     */	
	private $aesIv = null;

	/**
	 * Generate a valid randomic AES Key
	 *
	 * @return string Binary format of new AES generated key
	 *
	 * @ignore
	 */
	private function generateAESKey(){
		srand(); 
		$buffer = "";
		for($i = 0; $i < 100; $i++){
			$buffer .= chr(rand(97,122));
		}

		$buffer = md5($buffer);
		return hex2bin($buffer);
	}

    /**
     * Validate an hexadecimal string that will be used as AES key.
     * If valid, the binary form of the key will be provided, otherwise <i>CryptoParamsException</i> will be raised
     *
     * @param string $key Hexadecimal string that needs to be validated
     * @return string Binary form of Key
     * @throws CryptoParamsException If validation fails
     * 
     * @ignore
     */
	private function validateAESKey($key){
		if (!is_string($key)){
			throw new CryptoParamsException("AES Key should be a string");
		}
		if (strlen($key) != 32){
			throw new CryptoParamsException("AES Key has to be 32 bytes long");
		}
		if (!ctype_xdigit($key)){
			throw new CryptoParamsException("AES Key has to be expressed as hexadecimal string");	
		}
		return hex2bin($key);

	}

    /**
     * Validate an hexadecimal string that will be used as AES initialization vector.
     * If valid, the binary form of the initialization vector will be provided, otherwise <i>CryptoParamsException</i> will be raised
     *
     * @param string $iv Hexadecimal string that needs to be validated
     * @return string Binary form of initialization vector
     * @throws CryptoParamsException If validation fails
     * 
     * @ignore
     */
	private function validateAESIV($iv){
		if (!is_string($iv)){
			throw new CryptoParamsException("AES Initialization Vector should be a string");
		}
		if (strlen($iv) != 32){
			throw new CryptoParamsException("AES Initialization Vector has to be 32 bytes long");
		}
		if (!ctype_xdigit($iv)){
			throw new CryptoParamsException("AES Key has to be expressed as hexadecimal string");	
		}
		return hex2bin($iv);			

	}

    /**
     * Pad a string using PKCS7 Specification
     * 
     * @param string $str String that needs to be padded
     * @return string String paddes as per PKCS7 specs
     * @see http://www.ietf.org/rfc/rfc2315.txt PKCS7 RFC2315 (padding and unpadding)
     *
     * @ignore
     */
	private function padPKCS7($str){
		$block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'ncfb');
		$pad = $block - (strlen($str) % $block);
		$result = $str;
		$result .= str_repeat(chr($pad), $pad);
		return $result;
	}

    /**
     * Unpad a string using PKCS7 Specification
     * 
     * @param string $str String that needs to be unpadded
     * @return string String unpadded as per PKCS7 specs
     * @see http://www.ietf.org/rfc/rfc2315.txt PKCS7 RFC2315 (padding and unpadding)
     *
     * @ignore
     */
	private function unpadPKCS7($str){
		$block = mcrypt_get_block_size('des', 'ecb');
		$pad = ord($str[($len = strlen($str)) - 1]);
		return substr($str, 0, strlen($str) - $pad);
	}

    /**
     * @ignore
     */
	public function __get ($name){
		switch($name){
			case "key":{
				return bin2hex($this->aesKey);
			}
			case "iv":{
				return bin2hex($this->aesIv);
			}
		}
	}

    /**
     * @ignore
     */
	public function __set($name, $value){
		switch($name){
			case "key":{
				$this->aesKey = $this->validateAESKey($value);
			}
			case "iv":{
				$this->aesIv = $this->validateAESIV($value);
			}
		}
	}

	/**
	 * Encrypt a string using AES algorithm using the key and the initialization vector provided (or generated).
	 * Result is a base64 encoded string with encrypted data (to be easier to be manipulated as a parameter).
	 *
	 * @param string $str String that needs to be encrypted
	 * @return string Base64 encoed encripted string
	 * @throws CryptoParamsException if something went wrong
	 */
	public function encrypt($str){
		if (!is_string($str)){
			throw new CryptoParamsException("Value must be a string");
		}
		$result = $this->padPKCS7($str);
		$result = mcrypt_encrypt(
			MCRYPT_RIJNDAEL_128, 
			$this->aesKey, 
			$result, 
			'ncfb', 
			$this->aesIv
		);

		return trim(base64_encode($result));
	}

	/**
	 * Decrypt a base 64 encoded string using AES algorithm using the key and the initialization vector provided.
	 *
	 * @param string $str Base64 encoded string that needs to be decrypterd
	 * @return string Decrypted string
	 * @throws CryptoParamsException if something went wrong
	 */
	public function decrypt($str){
		if (!is_string($str)){
			throw new CryptoParamsException("Value must be a string");
		}		
		$result = base64_decode(trim($str));
		$result = mcrypt_decrypt(
			MCRYPT_RIJNDAEL_128, 
			$this->aesKey, 
			$result, 
			'ncfb', 
			$this->aesIv
		);
		return $this->unpadPKCS7($result);
	}

    /**
     * Initialize this class
     *
     * @param string $key Hexadecimal string that will be used as AES Key, if not provided a random one is generated
     * @param string $iv Hexadecimal string that will be used as initialization vector, if not provided a random one is generated
     * @throws CryptoParamsException if initialization fails
     */
    public function __construct($key=null, $iv=null) {
       if ($key == null){
       	$this->aesKey = $this->generateAESKey();
       }else{
       	$this->aesKey = $this->validateAESKey($key);
       }
       if ($iv == null){
       		$this->aesIv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, 'ncfb'), MCRYPT_RAND);
       }else{
       	$this->aesIv = $this->validateAESIV($iv);
       }
    }	

};
