<?php

require_once('crypto.php');
require_once('utils.php');
require_once('bip39words.php');
require_once('account.php');
require_once('transaction.php');
require_once('networking.php');
require_once('bytebuffer/main.php');
require_once('assert/lib/Assert/Assertion.php');
require_once('polyfill-mbstring/Mbstring.php');
require_once('polyfill-mbstring/Resources/unidata/lowerCase.php');
require_once('polyfill-mbstring/Resources/unidata/upperCase.php');
require_once('php-aes-gcm/src/AESGCM.php');
require_once('BigInteger.php');
require_once('const.php');


$m = new Memcache();
$m->addServer('localhost', 11211);
$lisk_host = $m->get('lisk_host');
$lisk_port = $m->get('lisk_port');
$lisk_protocol = $m->get('lisk_protocol');
if ($lisk_host && $lisk_port && $lisk_protocol) {
	$server = $lisk_protocol."://";
	if ($lisk_port == 80 || $lisk_port == 443) {
		$server .= $lisk_host."/";
	} else {
		$server .= $lisk_host.":".$lisk_port."/";
	}
} else {
	if (SECURE) {
		$server = "https://";
	} else {
		$server = "http://";
	}
	if (MAINNET) {
		$lisk_public_nodes = array("node01.lisk.io",
            					   "node02.lisk.io",
            					   "node03.lisk.io",
            					   "node04.lisk.io",
            					   "node05.lisk.io",
            					   "node06.lisk.io",
            					   "node07.lisk.io",
            					   "node08.lisk.io");
		$server .= $lisk_public_nodes[array_rand($lisk_public_nodes)]."/";
	} else {
		$GLOBALS['server'] .= "testnet.lisk.io/";
	}
}


?>