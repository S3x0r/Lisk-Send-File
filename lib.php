<?php /* Karek lisk-php */

const LISK_START = 1464109200;
const USER_AGENT = "Lisk Send File (Linux, en-GB)";
const SECURE = true;
const MAINNET = false;
const NETWORK_HASH = "198f2b61a8eb95fbeed58b8216780b68f697f26b849acf00c8c93bb9b24f783d";
const MINVERSION = ">=1.0.0";
const OS = "lisk-php-api";
const API_VERSION = "1.0.0";
const SEND_TRANSACTION_ENDPOINT = "api/transactions";
const SEND_FEE = 0.1 * 100000000;
const SEND_TRANSACTION_FLAG = 0;

$lisk_host = '';
$lisk_port = '';
$lisk_protocol = '';
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

function GetCurrentLiskTimestamp(){
	$current_timestamp = time();
	return $current_timestamp-LISK_START;
}

function CreateTransaction($recipientId, $amount, $passphrase1, $passphrase2, $data, $timeOffset, $type=SEND_TRANSACTION_FLAG, $asset=false){
	$keys = getKeysFromSecret($passphrase1);
	if (!$asset) {
		$asset = new stdClass();
	}
	if ($type == SEND_TRANSACTION_FLAG) {
		$fee = SEND_FEE;
	}

	if ($data) {
		$asset = array();
  $asset['data'] = (string)$data;
	}
	$time_difference = GetCurrentLiskTimestamp()+$timeOffset;
	$transaction = array('type' => $type,
						 'amount' => (string)$amount,
						 'fee' => (string)$fee,
						 'recipientId' => $recipientId,
						 'timestamp' => (int)$time_difference,
						 'asset' => $asset
						);
	$transaction['senderPublicKey'] = bin2hex($keys['public']);
	$signature = signTx($transaction,$keys);
	$transaction['signature'] = bin2hex($signature);
	if ($passphrase2) {
		$secondKeys = getKeysFromSecret($passphrase2);
		$signSignature = signTx($transaction,$secondKeys);
		$transaction['signSignature'] = bin2hex($signSignature);
	}
	$transaction['id'] = getTxId($transaction);
	return $transaction;
}

function SendTransaction($transaction_string,$server){
	$url = $server.SEND_TRANSACTION_ENDPOINT;
	return MainFunction("POST",$url,$transaction_string,true,true,30);
}

function MainFunction($method,$url,$body=false,$jsonBody=true,$jsonResponse=true,$timeout=3){
  $ch = curl_init($url);                                       
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$timeout);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  $headers =  array();
  if ($body) {  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);                                                             
	  if ($jsonBody) {
		  $headers = array('Content-Type: application/json','Content-Length: ' . strlen($body)); 
    }
  }
		  $port="443";

  array_push($headers, "minVersion: ".MINVERSION);
  array_push($headers, "os: ".OS);
  array_push($headers, "version: ".API_VERSION);
  array_push($headers, "port: ".$port);
  array_push($headers, "Accept-Language: en-GB");
  array_push($headers, "nethash: ".NETWORK_HASH);
  array_push($headers, "broadhash: ".NETWORK_HASH);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $result = curl_exec($ch);
  if ($jsonResponse) {
  	$result = json_decode($result, true); 
  }
  return $result;
}

function getKeysFromSecret($secret,$string=false) {
	$hash = hex2bin(hash('sha256', $secret));
	$kp = sodium_crypto_sign_seed_keypair($hash);
	$secret = sodium_crypto_sign_secretkey($kp);
	$public = sodium_crypto_sign_publickey($kp);
	if ($string) {
		$keys = array('public' => bin2hex($public),'secret' => bin2hex($secret));
	} else {
		$keys = array('public' => $public,'secret' => $secret);
	}
	return $keys;
}

function signTx($transaction, $keys) {
	$hash = hex2bin(getSignedTxBody($transaction));
	$signature = sodium_crypto_sign_detached($hash, $keys['secret']);
	return $signature;
}

function getSignedTxBody($transaction){
	$bytes = getTxAssetBytes($transaction);
	$assetSize = $bytes['assetSize'];
	$assetBytes = $bytes['assetBytes'];
	$body = assignTransactionBuffer($transaction, $assetSize, $assetBytes,'');
	return hash('sha256', $body);
}

function getTxId($transaction) {
	$bytes = getTxAssetBytes($transaction);
	$assetSize = $bytes['assetSize'];
	$assetBytes = $bytes['assetBytes'];
	$body = assignTransactionBuffer($transaction, $assetSize, $assetBytes,'');
	$hash = hash('sha256', $body);
	$byte_array = str_split($hash,2);
	$tmp = array();
	for ($i = 0; $i < 8; $i++) {
		$tmp[$i] = $byte_array[7 - $i];
	}
	$tmp = implode("",$tmp);
	$tmp = bchexdec($tmp);
	return $tmp;
}

function assignHexToBuffer($transactionBuffer, $hexValue) {
	$hexBuffer = str_split($hexValue,2);
	foreach ($hexBuffer as $key => $value) {
		$byte = bchexdec($value);
		$transactionBuffer->writeBytes([$byte]);
	}
	return $transactionBuffer;
}

function assignTransactionBuffer($transaction, $assetSize, $assetBytes, $options) {
		$transactionBuffer = BBStream::factory('');
		$transactionBuffer->isLittleEndian = false;
		$transactionBuffer->writeInt($transaction['type'], 8);//1
		$transactionBuffer->writeInt($transaction['timestamp']); //4
		$transactionBuffer = assignHexToBuffer($transactionBuffer, $transaction['senderPublicKey']);
		if (array_key_exists('requesterPublicKey', $transaction)) {//32
			assignHexToBuffer($transactionBuffer, $transaction['requesterPublicKey']);
		}
		if (array_key_exists('recipientId', $transaction)){ //8
			$recipient = $transaction['recipientId'];
			$recipient = substr($recipient, 0, -1);
			$recipient_bi = new Math_BigInteger($recipient);
			$bytes = unpack('C*',$recipient_bi->toBytes());
			$c = count($bytes);
			if ($c!=8) {
				for ($i = 0; $i < 8-$c; $i++) {
					$transactionBuffer->writeBytes([0]);
				}
				for ($i = 1; $i <= $c; $i++) {
					$transactionBuffer->writeBytes([$bytes[$i]]);
				}
			} else {
				for ($i = 1; $i <= 8; $i++) {
					$transactionBuffer->writeBytes([$bytes[$i]]);
				}
			}
		} else {
			for ($i = 0; $i <= 8; $i++) {
				$transactionBuffer->writeBytes([0]);
			}
		}
		$bytes = BBUtils::intToBytes($transaction['amount'],64);
		$bytes = array_reverse($bytes);
		$transactionBuffer->writeBytes($bytes);
		if (array_key_exists('data', $transaction)) {//64
			$transactionBuffer = assignHexToBuffer($transactionBuffer, $transaction['data']);//64
		}
		if ($assetSize > 0) {
			for ($i = 0; $i < $assetSize; $i++) {
				$transactionBuffer->writeBytes([$assetBytes[$i]]);
			}
		}
		if($options != 'multisignature') {
			if (array_key_exists('signature', $transaction)) {
				$transactionBuffer = assignHexToBuffer($transactionBuffer, $transaction['signature']);//64
			}
			if (array_key_exists('signSignature', $transaction)) {
				$transactionBuffer = assignHexToBuffer($transactionBuffer, $transaction['signSignature']);//64
			}
		}
		$transactionBuffer->rewind();
		$size = $transactionBuffer->size();
		$bytes = $transactionBuffer->readBytes($size);
		$string = call_user_func_array("pack", array_merge(array("C*"), $bytes));
		return $string;
}

function getTxAssetBytes($transaction){
	if ($transaction['type'] == SEND_TRANSACTION_FLAG) {
		if ($transaction['asset'] == new stdClass()) {
			$tmp = array('assetBytes' => null,
					 	 'assetSize' => 0);
		} else {
			$data = strtohex($transaction['asset']['data']);
			$hexBuffer = str_split($data,2);
			$byteBuffer = array();
			foreach ($hexBuffer as $key => $value) {
				$byte = bchexdec($value);
				$byteBuffer[] = $byte;
			}
			$tmp = array('assetBytes' => $byteBuffer,
					 	 'assetSize' => count($byteBuffer));
		}
	} 
	return $tmp;
}

function bchexdec($hex){
    $dec = 0;
    $len = strlen($hex);
    for ($i = 1; $i <= $len; $i++) {
        $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
    }
    return $dec;
}

function strtohex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}

define('MATH_BIGINTEGER_MONTGOMERY', 0);
define('MATH_BIGINTEGER_BARRETT', 1);
define('MATH_BIGINTEGER_POWEROF2', 2);
define('MATH_BIGINTEGER_CLASSIC', 3);
define('MATH_BIGINTEGER_NONE', 4);
define('MATH_BIGINTEGER_VALUE', 0);
define('MATH_BIGINTEGER_SIGN', 1);
define('MATH_BIGINTEGER_VARIABLE', 0);
define('MATH_BIGINTEGER_DATA', 1);
define('MATH_BIGINTEGER_MODE_INTERNAL', 1);
define('MATH_BIGINTEGER_MODE_BCMATH', 2);
define('MATH_BIGINTEGER_MODE_GMP', 3);
define('MATH_BIGINTEGER_KARATSUBA_CUTOFF', 25);

class Math_BigInteger
{
    var $value;
    var $is_negative = false;
    var $generator = 'mt_rand';
    var $precision = -1;
    var $bitmask = false;
    var $hex;

    function Math_BigInteger($x = 0, $base = 10)
    {
        if ( !defined('MATH_BIGINTEGER_MODE') ) {
            switch (true) {
                case extension_loaded('gmp'):
                    define('MATH_BIGINTEGER_MODE', MATH_BIGINTEGER_MODE_GMP);
                    break;
                case extension_loaded('bcmath'):
                    define('MATH_BIGINTEGER_MODE', MATH_BIGINTEGER_MODE_BCMATH);
                    break;
                default:
                    define('MATH_BIGINTEGER_MODE', MATH_BIGINTEGER_MODE_INTERNAL);
            }
        }

        if (function_exists('openssl_public_encrypt') && !defined('MATH_BIGINTEGER_OPENSSL_DISABLE') && !defined('MATH_BIGINTEGER_OPENSSL_ENABLED')) {
            ob_start();
            phpinfo();
            $content = ob_get_contents();
            ob_end_clean();

            preg_match_all('#OpenSSL (Header|Library) Version(.*)#im', $content, $matches);

            $versions = array();
            if (!empty($matches[1])) {
                for ($i = 0; $i < count($matches[1]); $i++) {
                    $versions[$matches[1][$i]] = trim(str_replace('=>', '', strip_tags($matches[2][$i])));
                }
            }
            switch (true) {
                case !isset($versions['Header']):
                case !isset($versions['Library']):
                case $versions['Header'] == $versions['Library']:
                    define('MATH_BIGINTEGER_OPENSSL_ENABLED', true);
                    break;
                default:
                    define('MATH_BIGINTEGER_OPENSSL_DISABLE', true);
            }
        }

        if (!defined('PHP_INT_SIZE')) {
            define('PHP_INT_SIZE', 4);
        }

        if (!defined('MATH_BIGINTEGER_BASE') && MATH_BIGINTEGER_MODE == MATH_BIGINTEGER_MODE_INTERNAL) {
            switch (PHP_INT_SIZE) {
                case 8: // use 64-bit integers if int size is 8 bytes
                    define('MATH_BIGINTEGER_BASE',       31);
                    define('MATH_BIGINTEGER_BASE_FULL',  0x80000000);
                    define('MATH_BIGINTEGER_MAX_DIGIT',  0x7FFFFFFF);
                    define('MATH_BIGINTEGER_MSB',        0x40000000);
                    define('MATH_BIGINTEGER_MAX10',      1000000000);
                    define('MATH_BIGINTEGER_MAX10_LEN',  9);
                    define('MATH_BIGINTEGER_MAX_DIGIT2', pow(2, 62));
                    break;

                default:
                    define('MATH_BIGINTEGER_BASE',       26);
                    define('MATH_BIGINTEGER_BASE_FULL',  0x4000000);
                    define('MATH_BIGINTEGER_MAX_DIGIT',  0x3FFFFFF);
                    define('MATH_BIGINTEGER_MSB',        0x2000000);
                    define('MATH_BIGINTEGER_MAX10',      10000000);
                    define('MATH_BIGINTEGER_MAX10_LEN',  7);
                    define('MATH_BIGINTEGER_MAX_DIGIT2', pow(2, 52));
            }
        }

        switch ( MATH_BIGINTEGER_MODE ) {
            case MATH_BIGINTEGER_MODE_GMP:
                if (is_resource($x) && get_resource_type($x) == 'GMP integer') {
                    $this->value = $x;
                    return;
                }
                $this->value = gmp_init(0);
                break;
            case MATH_BIGINTEGER_MODE_BCMATH:
                $this->value = '0';
                break;
            default:
                $this->value = array();
        }

        if (empty($x) && (abs($base) != 256 || $x !== '0')) {
            return;
        }

        switch ($base) {
            case -256:
                if (ord($x[0]) & 0x80) {
                    $x = ~$x;
                    $this->is_negative = true;
                }
            case  256:
                switch ( MATH_BIGINTEGER_MODE ) {
                    case MATH_BIGINTEGER_MODE_GMP:
                        $sign = $this->is_negative ? '-' : '';
                        $this->value = gmp_init($sign . '0x' . bin2hex($x));
                        break;
                    case MATH_BIGINTEGER_MODE_BCMATH:
                        $len = (strlen($x) + 3) & 0xFFFFFFFC;
                        $x = str_pad($x, $len, chr(0), STR_PAD_LEFT);

                        for ($i = 0; $i < $len; $i+= 4) {
                            $this->value = bcmul($this->value, '4294967296', 0);
                            $this->value = bcadd($this->value, 0x1000000 * ord($x[$i]) + ((ord($x[$i + 1]) << 16) | (ord($x[$i + 2]) << 8) | ord($x[$i + 3])), 0);
                        }

                        if ($this->is_negative) {
                            $this->value = '-' . $this->value;
                        }

                        break;
                    default:
                        while (strlen($x)) {
                            $this->value[] = $this->_bytes2int($this->_base256_rshift($x, MATH_BIGINTEGER_BASE));
                        }
                }

                if ($this->is_negative) {
                    if (MATH_BIGINTEGER_MODE != MATH_BIGINTEGER_MODE_INTERNAL) {
                        $this->is_negative = false;
                    }
                    $temp = $this->add(new Math_BigInteger('-1'));
                    $this->value = $temp->value;
                }
                break;
            case  16:
            case -16:
                if ($base > 0 && $x[0] == '-') {
                    $this->is_negative = true;
                    $x = substr($x, 1);
                }

                $x = preg_replace('#^(?:0x)?([A-Fa-f0-9]*).*#', '$1', $x);

                $is_negative = false;
                if ($base < 0 && hexdec($x[0]) >= 8) {
                    $this->is_negative = $is_negative = true;
                    $x = bin2hex(~pack('H*', $x));
                }

                switch ( MATH_BIGINTEGER_MODE ) {
                    case MATH_BIGINTEGER_MODE_GMP:
                        $temp = $this->is_negative ? '-0x' . $x : '0x' . $x;
                        $this->value = gmp_init($temp);
                        $this->is_negative = false;
                        break;
                    case MATH_BIGINTEGER_MODE_BCMATH:
                        $x = ( strlen($x) & 1 ) ? '0' . $x : $x;
                        $temp = new Math_BigInteger(pack('H*', $x), 256);
                        $this->value = $this->is_negative ? '-' . $temp->value : $temp->value;
                        $this->is_negative = false;
                        break;
                    default:
                        $x = ( strlen($x) & 1 ) ? '0' . $x : $x;
                        $temp = new Math_BigInteger(pack('H*', $x), 256);
                        $this->value = $temp->value;
                }

                if ($is_negative) {
                    $temp = $this->add(new Math_BigInteger('-1'));
                    $this->value = $temp->value;
                }
                break;
            case  10:
            case -10:
                $x = preg_replace('#(?<!^)(?:-).*|(?<=^|-)0*|[^-0-9].*#', '', $x);

                switch ( MATH_BIGINTEGER_MODE ) {
                    case MATH_BIGINTEGER_MODE_GMP:
                        $this->value = gmp_init($x);
                        break;
                    case MATH_BIGINTEGER_MODE_BCMATH:
                        $this->value = $x === '-' ? '0' : (string) $x;
                        break;
                    default:
                        $temp = new Math_BigInteger();

                        $multiplier = new Math_BigInteger();
                        $multiplier->value = array(MATH_BIGINTEGER_MAX10);

                        if ($x[0] == '-') {
                            $this->is_negative = true;
                            $x = substr($x, 1);
                        }

                        $x = str_pad($x, strlen($x) + ((MATH_BIGINTEGER_MAX10_LEN - 1) * strlen($x)) % MATH_BIGINTEGER_MAX10_LEN, 0, STR_PAD_LEFT);
                        while (strlen($x)) {
                            $temp = $temp->multiply($multiplier);
                            $temp = $temp->add(new Math_BigInteger($this->_int2bytes(substr($x, 0, MATH_BIGINTEGER_MAX10_LEN)), 256));
                            $x = substr($x, MATH_BIGINTEGER_MAX10_LEN);
                        }

                        $this->value = $temp->value;
                }
                break;
            case  2: // base-2 support originally implemented by Lluis Pamies - thanks!
            case -2:
                if ($base > 0 && $x[0] == '-') {
                    $this->is_negative = true;
                    $x = substr($x, 1);
                }

                $x = preg_replace('#^([01]*).*#', '$1', $x);
                $x = str_pad($x, strlen($x) + (3 * strlen($x)) % 4, 0, STR_PAD_LEFT);

                $str = '0x';
                while (strlen($x)) {
                    $part = substr($x, 0, 4);
                    $str.= dechex(bindec($part));
                    $x = substr($x, 4);
                }

                if ($this->is_negative) {
                    $str = '-' . $str;
                }

                $temp = new Math_BigInteger($str, 8 * $base); // ie. either -16 or +16
                $this->value = $temp->value;
                $this->is_negative = $temp->is_negative;

                break;
            default:
        }
    }

    function toBytes($twos_compliment = false)
    {
        if ($twos_compliment) {
            $comparison = $this->compare(new Math_BigInteger());
            if ($comparison == 0) {
                return $this->precision > 0 ? str_repeat(chr(0), ($this->precision + 1) >> 3) : '';
            }

            $temp = $comparison < 0 ? $this->add(new Math_BigInteger(1)) : $this->copy();
            $bytes = $temp->toBytes();

            if (empty($bytes)) {
                $bytes = chr(0);
            }

            if (ord($bytes[0]) & 0x80) {
                $bytes = chr(0) . $bytes;
            }

            return $comparison < 0 ? ~$bytes : $bytes;
        }

        switch ( MATH_BIGINTEGER_MODE ) {
            case MATH_BIGINTEGER_MODE_GMP:
                if (gmp_cmp($this->value, gmp_init(0)) == 0) {
                    return $this->precision > 0 ? str_repeat(chr(0), ($this->precision + 1) >> 3) : '';
                }

                $temp = gmp_strval(gmp_abs($this->value), 16);
                $temp = ( strlen($temp) & 1 ) ? '0' . $temp : $temp;
                $temp = pack('H*', $temp);

                return $this->precision > 0 ?
                    substr(str_pad($temp, $this->precision >> 3, chr(0), STR_PAD_LEFT), -($this->precision >> 3)) :
                    ltrim($temp, chr(0));
            case MATH_BIGINTEGER_MODE_BCMATH:
                if ($this->value === '0') {
                    return $this->precision > 0 ? str_repeat(chr(0), ($this->precision + 1) >> 3) : '';
                }

                $value = '';
                $current = $this->value;

                if ($current[0] == '-') {
                    $current = substr($current, 1);
                }

                while (bccomp($current, '0', 0) > 0) {
                    $temp = bcmod($current, '16777216');
                    $value = chr($temp >> 16) . chr($temp >> 8) . chr($temp) . $value;
                    $current = bcdiv($current, '16777216', 0);
                }

                return $this->precision > 0 ?
                    substr(str_pad($value, $this->precision >> 3, chr(0), STR_PAD_LEFT), -($this->precision >> 3)) :
                    ltrim($value, chr(0));
        }

        if (!count($this->value)) {
            return $this->precision > 0 ? str_repeat(chr(0), ($this->precision + 1) >> 3) : '';
        }
        $result = $this->_int2bytes($this->value[count($this->value) - 1]);

        $temp = $this->copy();

        for ($i = count($temp->value) - 2; $i >= 0; --$i) {
            $temp->_base256_lshift($result, MATH_BIGINTEGER_BASE);
            $result = $result | str_pad($temp->_int2bytes($temp->value[$i]), strlen($result), chr(0), STR_PAD_LEFT);
        }

        return $this->precision > 0 ?
            str_pad(substr($result, -(($this->precision + 7) >> 3)), ($this->precision + 7) >> 3, chr(0), STR_PAD_LEFT) :
            $result;
    }

    function compare($y)
    {
        switch ( MATH_BIGINTEGER_MODE ) {
            case MATH_BIGINTEGER_MODE_GMP:
                return gmp_cmp($this->value, $y->value);
            case MATH_BIGINTEGER_MODE_BCMATH:
                return bccomp($this->value, $y->value, 0);
        }

        return $this->_compare($this->value, $this->is_negative, $y->value, $y->is_negative);
    }

    function _compare($x_value, $x_negative, $y_value, $y_negative)
    {
        if ( $x_negative != $y_negative ) {
            return ( !$x_negative && $y_negative ) ? 1 : -1;
        }

        $result = $x_negative ? -1 : 1;

        if ( count($x_value) != count($y_value) ) {
            return ( count($x_value) > count($y_value) ) ? $result : -$result;
        }
        $size = max(count($x_value), count($y_value));

        $x_value = array_pad($x_value, $size, 0);
        $y_value = array_pad($y_value, $size, 0);

        for ($i = count($x_value) - 1; $i >= 0; --$i) {
            if ($x_value[$i] != $y_value[$i]) {
                return ( $x_value[$i] > $y_value[$i] ) ? $result : -$result;
            }
        }

        return 0;
    }
}


class BBStream
{
    public $options = [];
    public $isLittleEndian = true;

    protected $_handle;

    protected function __construct($stream, $options = [])
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Stream must be a resource');
        }
        $this->_handle = $stream;
        $this->options = $options;
    }

    public static function factory($resource = '', $options = [])
    {
        $type = gettype($resource);
        switch ($type) {
            case 'string':
                $stream = fopen('php://temp', 'r+');
                if ($resource !== '') {
                    fwrite($stream, $resource);
                    fseek($stream, 0);
                }
                return new static($stream, $options);
            case 'resource':
                return new static($resource, $options);
            case 'object':
                if (method_exists($resource, '__toString')) {
                    return static::factory((string)$resource, $options);
                }
        }
        throw new \InvalidArgumentException(sprintf('Invalid resource type: %s', $type));
    }

    public function getMetaData()
    {
        return stream_get_meta_data($this->_handle);
    }

    public function getResource()
    {
        return $this->_handle;
    }

    public function size()
    {
        $currPos = ftell($this->_handle);
        fseek($this->_handle, 0, SEEK_END);
        $length = ftell($this->_handle);
        fseek($this->_handle, $currPos, SEEK_SET);
        return $length;
    }

    public function allocate($length, $skip = true)
    {
        $stream = fopen('php://memory', 'r+');
        if (stream_copy_to_stream($this->_handle, $stream, $length)) {
            if ($skip) {
                $this->skip($length);
            }
            return new static($stream);
        }
        throw new Exception('Buffer allocation failed');
    }

    public function pipe($resource, $length = null)
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('Invalid resource type');
        }
        if ($length) {
            return stream_copy_to_stream($resource, $this->_handle, $length);
        } else {
            return stream_copy_to_stream($resource, $this->_handle);
        }
    }

    public function offset()
    {
        return ftell($this->_handle);
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->_handle, $offset, $whence);
    }

    public function rewind()
    {
        return rewind($this->_handle);
    }

    public function skip($length)
    {
        return $this->seek($length, SEEK_CUR);
    }

    public function read($length = null)
    {
        return stream_get_contents($this->_handle, $length, $this->offset());
    }

    public function readLine($length = null, $ending = "\n")
    {
        if ($length === null) {
            $length = $this->size();
        }
        return stream_get_line($this->_handle, $length, $ending);
    }

    public function write($data, $length = null)
    {
        if ($length === null) {
            return fwrite($this->_handle, $data);
        } else {
            return fwrite($this->_handle, $data, $length);
        }
    }

    public function writeBytes($bytes)
    {
        array_unshift($bytes, 'C*');
        return $this->write(call_user_func_array('pack', $bytes));
    }

    public function readBytes($length)
    {
        $bytes = $this->read($length);
        if ($bytes !== false) {
            return array_values(unpack('C*', $bytes));
        }
        return false;
    }

    public function writeString($value, $length = '*', $charset = null)
    {
        if ($charset) {
            $value = iconv('utf8', $charset, $value);
        } elseif (isset($this->options['charset'])) {
            $value = iconv('utf8', $this->options['charset'], $value);
        }
        return $this->write(pack('A' . $length, $value));
    }

    public function readString($length, $charset = null)
    {
        $bytes = $this->read($length);
        $value = unpack('A' . $length, $bytes)[1];
        if ($charset) {
            $value = iconv($charset, 'utf8', $value);
        } elseif ($this->options['charset']) {
            $value = iconv($this->options['charset'], 'utf8', $value);
        }
        return $value;
    }

    public function writeInt($value, $size = 32)
    {
        $bytes = BBUtils::intToBytes($value, $size);
        if (!$this->isLittleEndian) {
            $bytes = array_reverse($bytes);
        }
        array_unshift($bytes, 'C*');
        return $this->write(call_user_func_array('pack', $bytes));
    }

    public function readInt($size = 32, $unsigned = true)
    {
        $size = BBUtils::roundUp($size, 8);
        $data = $this->read($size / 8);
        $value = 0;
        switch ($size) {
            case 8:
                $value = unpack('C', $data)[1];
                break;
            case 16:
                $value = unpack($this->isLittleEndian ? 'v' : 'n', $data)[1];
                break;
            case 24:
                $bytes = unpack('C3', $data);
                if ($this->isLittleEndian) {
                    $value = $bytes[1] | $bytes[2] << 8 | $bytes[3] << 16;
                } else {
                    $value = $bytes[1] << 16 | $bytes[2] << 8 | $bytes[3];
                }
                break;
            case 32:
                $value = unpack($this->isLittleEndian ? 'V' : 'N', $data)[1];
                break;
            case 64:
                $ret = unpack($this->isLittleEndian ? 'V2' : 'N2', $data);
                if ($this->isLittleEndian) {
                    $value = bcadd($ret[1], bcmul($ret[2], 0xffffffff + 1));
                } else {
                    $value = bcadd($ret[2], bcmul($ret[1], 0xffffffff + 1));
                }
                break;
        }
        return $unsigned ? $value : BBUtils::unsignedToSigned($value, $size);
    }

    public function writeBool($value)
    {
        return $this->writeInt($value ? 1 : 0, 8);
    }

    public function readBool()
    {
        return $this->readInt(8);
    }

    public function writeFloat($value)
    {
        $bytes = pack('f', $value);
        return $this->write($bytes);
    }

    public function readFloat()
    {
        $bytes = $this->read(4);
        return unpack('f', $bytes)[1];
    }

    public function writeDouble($value)
    {
        $bytes = pack('d', $value);
        return $this->write($bytes);
    }

    public function readDouble()
    {
        $bytes = $this->read(8);
        return unpack('d', $bytes)[1];
    }

    public function writeNull($length)
    {
        return $this->write(pack('x' . $length));
    }

    public function save($file)
    {
        $this->rewind();
        return file_put_contents($file, $this->_handle);
    }

    public function close()
    {
        if (is_resource($this->_handle)) {
            fclose($this->_handle);
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}

class BBUtils
{
    public static function roundUp($x, $y)
    {
        return ceil($x / $y) * $y;
    }

    public static function roundDown($x, $y)
    {
        return floor($x / $y) * $y;
    }

    public static function bytesToString(array $bytes)
    {
        $str = '';
        foreach ($bytes as $byte) $str .= chr($byte);
        return $str;
    }

    public static function doubleToString($num)
    {
        return self::bytesToString(unpack('C8', pack('d', $num)));
    }

    public static function stringToDouble($str)
    {
        if (strlen($str) < 8) {
            throw new \Exception('String must be a 8 length');
        }
        return unpack('d', pack('A8', $str))[1];
    }

    public static function bytesToInt(array $bytes, $unsigned = true)
    {
        $bytes = array_reverse($bytes);
        $value = 0;
        foreach ($bytes as $i => $b) {
            $value |= $b << $i * 8;
        }
        return $unsigned ? $value : self::unsignedToSigned($value, count($bytes) * 8);
    }

    public static function intToBytes($int, $size = 32)
    {
        $size = self::roundUp($size, 8);
        $bytes = [];
        for ($i = 0; $i < $size; $i += 8) {
            $bytes[] = 0xFF & $int >> $i;
        }
        $bytes = array_reverse($bytes);
        return $bytes;
    }

    public static function unsignedToSigned($value, $size = 32)
    {
        $size = self::roundUp($size, 8);
        if (bccomp($value, bcpow(2, $size - 1)) >= 0) {
            $value = bcsub($value, bcpow(2, $size));
        }
        return $value;
    }

    public static function signedToUnsigned($value, $size = 32)
    {
        return $value + bcpow(2, $size);
    }
}