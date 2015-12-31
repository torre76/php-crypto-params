<?php

require __DIR__ . '/CryptoParams.php';
// Autoload PHP Unit though Composer
require __DIR__ . '/vendor/autoload.php';

class CryptoParamsTest extends PHPUnit_Framework_TestCase{

	public function testInitialization(){
		$cp = new \CryptoParams\CryptoParams();
		$this->assertTrue(is_string($cp->key));
		$this->assertTrue(is_string($cp->iv));

		$cp = new \CryptoParams\CryptoParams("d0540d01397444a5f368185bfcb5b66b", "a1e1eb2a20241234a1e1eb2a20241234");
		$this->assertEquals($cp->key, "d0540d01397444a5f368185bfcb5b66b");
		$this->assertEquals($cp->iv, "a1e1eb2a20241234a1e1eb2a20241234");
	}

}