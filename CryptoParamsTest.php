<?php

require __DIR__ . '/CryptoParams.php';
// Autoload PHP Unit though Composer
require __DIR__ . '/vendor/autoload.php';

class CryptoParamsTest extends PHPUnit_Framework_TestCase{

	public function testInitialization(){
		$cp = new \CryptoParams\CryptoParams();
		$this->assertTrue(is_string($cp->key));
		$this->assertTrue(is_string($cp->iv));

		$cp = new \CryptoParams\CryptoParams();
		$cp->key = "d0540d01397444a5f368185bfcb5b66b";
		$cp->iv = "a1e1eb2a20241234a1e1eb2a20241234";
		$this->assertEquals($cp->key, "d0540d01397444a5f368185bfcb5b66b");
		$this->assertEquals($cp->iv, "a1e1eb2a20241234a1e1eb2a20241234");
	}

	public function testInvalidInitializationKeyLengthMismatch(){
		$this->setExpectedException('CryptoParams\CryptoParamsException');
		$cp = new \CryptoParams\CryptoParams("ouch");
	}

	public function testInvalidInitializationKeyNotString(){
		$this->setExpectedException('CryptoParams\CryptoParamsException');
		$cp = new \CryptoParams\CryptoParams(array("ouch"));
	}	

	public function testInvalidInitializationKeyNotHex(){
		$this->setExpectedException('CryptoParams\CryptoParamsException');
		$cp = new \CryptoParams\CryptoParams("aie1eb2a20241234a1e1eb2a20241234");
	}	

	public function testInvalidInitializationIVLengthMismatch(){
		$this->setExpectedException('CryptoParams\CryptoParamsException');
		$cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "ouch");
	}

	public function testInvalidInitializationIVNotString(){
		$this->setExpectedException('CryptoParams\CryptoParamsException');
		$cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", array("ouch"));
	}	

	public function testInvalidInitializationIVNotHex(){
		$this->setExpectedException('CryptoParams\CryptoParamsException');
		$cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "aie1eb2a20241234a1e1eb2a20241234");
	}	

	public function testSimpleEncryption(){
		$cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "a1e1eb2a20241234a1e1eb2a20241234");
		$encrypted = $cp->encrypt("aieiebrazorf");
		$this->assertEquals($encrypted, "iW8qzzEWpWRN0NPNoOwu3A==");	
	}	

	public function testSimpleEncryptionFailure(){
		$this->setExpectedException('CryptoParams\CryptoParamsException');		
		$cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "a1e1eb2a20241234a1e1eb2a20241234");
		$encrypted = $cp->encrypt(array("aieiebrazorf"));
	}		

}