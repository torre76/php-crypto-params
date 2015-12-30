<?php

namespace CryptoParams;

// Hex2Bin is not available on PHP 5.3
if ( !function_exists( 'hex2bin' ) ) {
    function hex2bin( $str ) {
        $sbin = "";
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i += 2 ) {
            $sbin .= pack( "H*", substr( $str, $i, 2 ) );
        }

        return $sbin;
    }
}

if ( !function_exists( 'bin2hex' ) ) {
	function bin2hex($h){
  		if (!is_string($h)){
  			return FALSE;
  		}
  		$r='';
  		for ($a=0; $a<strlen($h); $a+=2) {
  			$r.=chr(hexdec($h{$a}.$h{($a+1)})); 
  		}
  		return $r;
  	}
}

class CryptoParams {

	private $aesKey = null;
	private $aesIv = null;

	private function generateAESKey(){
		srand(); 
		$buffer = "";
		for($i = 0; $i < 100; $i++){
			$buffer .= chr(rand(97,122));
		}

		$buffer = md5($buffer);
		return hex2bin($buffer);
	}

	private function validateAESKey($key){
		if (!is_string($key)){
			throw new Exception("AES Key should be a string");
		}
		if (strlen($key) != 32){
			throw new Exception("AES Key has to be 32 bytes long");
		}
		if (hex2bin($key) === FALSE){
			throw new Exception("AES Key has to be expressed as hexadecimal string");
		}
		return hex2bin($key);
	}

	private function validateAESIV($iv){
		if (!is_string($iv)){
			throw new Exception("AES Initialization Vector should be a string");
		}
		if (strlen($iv) != 32){
			throw new Exception("AES Initialization Vector has to be 32 bytes long");
		}
		if (hex2bin($iv) === FALSE){
			throw new Exception("AES Initialization Vector has to be expressed as hexadecimal string");
		}
		return hex2bin($iv);
	}

	private function padPKCS7($str){
		$block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, 'ncfb');
		$pad = $block - (strlen($str) % $block);
		$result = $str;
		$result .= str_repeat(chr($pad), $pad);
		return $result;
	}

	private function unpadPKCS7($str){
		$block = mcrypt_get_block_size('des', 'ecb');
		$pad = ord($str[($len = strlen($str)) - 1]);
		return substr($str, 0, strlen($str) - $pad);
	}

	public function __get ($name){
		switch($name){
			case "key":{
				if ($this->aesKey != null){
					return bin2hex($this->aesKey);
				}else{
					return null;
				}
			}
			case "iv":{
				if ($this->aesIv != null){
					return bin2hex($this->aesIv);
				}else{
					return null;
				}
			}
		}
	}

	public function encrypt($str){
		if (!is_string($str)){
			throw new Exception("Value must be a string");
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

	public function decrypt($str){
		if (!is_string($str)){
			throw new Exception("Value must be a string");
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


// Test for now
$cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "a1e1eb2a20241234a1e1eb2a20241234");
//$cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", null);

var_dump($cp->key);
var_dump($cp->iv);
var_dump($cp->encrypt("aieiebrazorf")); // 'iW8qzzEWpWRN0NPNoOwu3A==' == 'iW8qzzEWpWRN0NPNoOwu3A=='
var_dump($cp->decrypt($cp->encrypt("aieiebrazorf")));
