<?php

error_reporting(0);

ini_set('precision', 25);

require_once('lib.php');
require_once('config.php');

define('N', PHP_EOL);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    chdir('../');
    $GLOBALS['OS'] = 'WIN';
}

echo '
 B@B@B@B@@@B@B@B@B@B@B@B@@@B@B@B@B@B@@@@@@@B@B@B@B@B@@@B@B
 @B@BGB@B@B@B@B@@@B@@@B@B@B@@@B@B@B@B@B@@@B@B@B@@@B@@@@@B@
 B@B@  :@Bi:@B@B@B@@@B@BGS522s22SXMB@B@B@B@B@B@B@B@@@B@B@B
 @: r   :   H@B@B@B@9sr;rrs5ssss2H2229M@@@B@B@B@B@B@B@B@@@
 B         S@B@@@B,      ,::rsGB5:,  ,:i9@@B@B@B@B@B@, B@B
 @B@M,     @B@X@X   rMB@Mr:,:MS          iB@B@B2  B@   @@@
 B@@@B@    :@BGB  sB@B@;sBBrii  rB@B@B2:, :B@B@i         s
 @@@B@@@ii:sB@9X ,@@B,    BSi  9Bi ,B@B@r,  M@B@B        S
 B@@@B@B@92,@9,X  @B@,   ,@2i  @     B@GX:,  B@@,     X@@B
 @B@@@B@BMs:r@r;i i@B@G2M@S::, @s  ,X@G92,   ,B@    B@B@B@
 @@B@B@M@B2r:sssr: i29@B5i,  r :@B@B@BXr,,   ,@;::rM@B@B@B
 @B@B@B@B@Gs:rHSSsi:,,,,     ,:,,rssri,,,iir,9s  rB@B@B@B@
 B@B@B@B@B@si:XSSSsrsi::,,,::,:::,,,, ,,:;rsr,  :B@B@B@B@B
 @B@B@B@@@BG: :XXG: :rssssS3x0rS2ssr::irrrrrr  ,B@B@B@B@B@
 B@B@B@B@B@Bs  :SGM                 :rrrsr,    G@B@@@B@B@@
 @B@@@B@B@B@Xs  :SM@               ,ssss,     r@B@B@B@B@B@
 B@B@B@@@B@B2Hs  :SM@@sr:,      :sMG22s,   ,r:@@@B@B@B@B@B
 @B@B@B@B@B@2s9s,  ,::r222sHSX222srri:   ,rrirB@B@B@B@B@B@
 B@B@B@B@B@B2s292                       :rri:2@B@B@B@B@B@B
 @B@B@B@@@B@Ss29s,  ,, ,         ,     rrrii,M@@B@@@B@B@B@
 B@B@B@B@B@@MsXGs,,,,, ,,:i:,,,       ,ssrriiB@B@B@@@B@B@B
 @B@B@B@@@B@r:r5r ,,,, ,,,,, ,,       ,rii:,,@B@B@@@B@B@B@
 B@B@B@B@B@@:   ,,:,,,,          ,,          G@@@B@B@B@B@B
 @B@B@B@B@B@B   ,,,,,,,,   ,                X@B@B@B@B@B@@@
 B@B@B@B@B@B@B        , , ,,               9@B@B@B@B@B@B@B
 @B@B@@@B@B@B@Br                         i@@B@B@B@B@B@B@B@
 B@B@B@B@B@@@B@B@Br:                  rM@B@B@B@B@B@B@B@B@@
 @B@B@B@B@@@B@B@@@B@B@2           :GB@BBG9XXSSS9X9999G9GGM
 B@B@@@B@B@B@B@@@B@B@@s           Srri;i;rrrssssssss22S5HS
 @B@B@B@B@B@BBMMGG9G:              :,::::iir;rs22SXGGMMMMB'.N.N.N;

echo " Lisk Send 1.2 (send file to Lisk blockchain)\n".
     " by minionsteam.org, phoenix1969, minions\n".
     " ------------------------------------------------------\n";

if (empty($GLOBALS['ADDRESS']) or empty($GLOBALS['PASSWORD'])) {
    echo "\n You need to configure config.php! Exiting.\n";
    WinSleep(5);
    exit;
}

!is_dir('tmp/') ? mkdir('tmp/') : false;

if (count(glob("tmp/*")) !== 0 && is_file('temp_meta')) {
    echo "\n Detected that last file send was not ended".
         "\n Do you want to resume sending file? (yes/no): ";
    $answer = Interact();
       
    if ($answer == 'yes' xor $answer == 'y') {
        echo N;
        $GLOBALS['resume'] = 'yes';
   
        $handle = file_get_contents('temp_meta');
        $temp_meta = explode('/', $handle);
            
        $GLOBALS['tmp_meta_filename'] = $temp_meta[0];
        $GLOBALS['tmp_meta_filesize'] = $temp_meta[1];
       
        Start();
    } else {
             DeleteTempFiles();
             Start('normal');
    }
} else {
         DeleteTempFiles();
         Start('normal');
}
//---------------------------------------------------------------------------------------------------
function Start($option = '')
{
    if ($option == 'normal') {
        echo "\n File to send: ";

        $GLOBALS['file'] = Interact();

        if (is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.$GLOBALS['file'])) {
            echo "\n File size         : ".formatBytes(filesize($GLOBALS['file']))."\n";
    
            /* zip file */
            echo " Compressing file...\n";
            $zip = new ZipArchive();
            $filename = './encoded_file';
            $zip->open($filename, ZipArchive::CREATE);
            $zip->addFile($GLOBALS['file']);
            $zip->close();

            $file_content = file_get_contents('encoded_file');

            /* save to file */
            file_put_contents('encoded_file', Base91::encode($file_content));
  
            echo " Encoded file size : ".formatBytes(filesize('encoded_file')).
                 "\n Splitting file...\n";

            Split('encoded_file', 'tmp/');
        } else {
                 echo "\n File does not exist or is located in another directory\n".
                      " file to be sent must be in the same directory as tool directory, Exiting.\n";
                 WinSleep(5);
                 exit;
        }
    }
        echo N;
        ini_restore('precision');
        $cost = (0.1 * CountFiles('tmp/')) + 0.1;
        echo " Transaction cost  : {$cost} lsk";
        ini_set('precision', 25);
        echo "\n Tx(s) to send     : ".(1 + CountFiles('tmp/'))."\n".
             " --------------------------------------------------\n\n".
             " Proceed? (yes/no) : ";

        $answer = Interact();

    if ($answer == 'yes' xor $answer == 'y') {
        /* Send txs */
        echo N;
        SendData();
    } else {
             DeleteTempFiles();
    }
}
//---------------------------------------------------------------------------------------------------
function WinSleep($time)
{
    isset($GLOBALS['OS']) ? sleep($time) : false;
}
//---------------------------------------------------------------------------------------------------
function Interact()
{
    while ($ask = fgets(STDIN)) {
           break;
    }
    $ask = trim($ask);

    return $ask;
}
//---------------------------------------------------------------------------------------------------
function SendData()
{
    $files = glob('tmp/'."*");
    natsort($files);

    $last_key = end(array_keys($files));

    foreach ($files as $key => $file) {
        if ($key == $last_key) {
            /* last tx */
            $data = file_get_contents($file)."'".toHex($id);
            $tx = CreateTransaction($GLOBALS['ADDRESS'], '1', $GLOBALS['PASSWORD'], false, $data, -10);
            $id = $tx['id'];

            $result = SendTransaction(json_encode($tx), $GLOBALS['server']);

            if ($result['data']['message'] == 'Transaction(s) accepted') {
                echo ' Left to send: '.CountFiles('tmp/').' tx(s)'." \r";

                /* send meta data */
                echo N.' Sending Meta Data...'.N;
 
                /* meta data */
                !isset($GLOBALS['resume']) ? $data = Base91::encode("M'".$GLOBALS['file']."'".filesize('encoded_file')."'".toHex($id)) :
                                             $data = Base91::encode("M'".$GLOBALS['tmp_meta_filename']."'".filesize('encoded_file')."'".toHex($id));

                $tx = CreateTransaction($GLOBALS['ADDRESS'], '1', $GLOBALS['PASSWORD'], false, $data, -10);
                $result = SendTransaction(json_encode($tx), $GLOBALS['server']);

                if ($result['data']['message'] == 'Transaction(s) accepted') {
                    echo " Done.\n";
                    /* Deleting temp files */
                    unlink('encoded_file');
                    unlink('temp_meta');
                    unlink($file);

                    echo "\n Your Data ID for file: {$tx['id']}\n";
                    if (!empty($GLOBALS['OS'])) {
                        echo "\n You can close this window...";
                        WinSleep(999);
                    } else {
                             exit;
                    }
                }
            } else {
                     echo " ERROR:\n";
                     var_dump($tx);
                     var_dump($result);
					 echo "\n End of ERROR response, Exiting.";
					 WinSleep(15);
                     exit;
            }
//---------------------------------------------------------------------------------------------------
        } else {
            if (isset($GLOBALS['resume'])) {
                $handle = file_get_contents('temp_meta');
                $temp_meta = explode('/', $handle);
                $id = $temp_meta[2];
            }

            empty($id) ? /* first tx */  $data = file_get_contents($file) :
                         /* rest tx's */ $data = file_get_contents($file)."'".toHex($id);

            $tx = CreateTransaction($GLOBALS['ADDRESS'], '1', $GLOBALS['PASSWORD'], false, $data, -10);
            $id = $tx['id'];

            $result = SendTransaction(json_encode($tx), $GLOBALS['server']);
            if ($result['data']['message'] == 'Transaction(s) accepted') {
                /* delete sended file piece */
                unlink($file);
                /* write data for meta needed to resume */
                !isset($GLOBALS['resume']) ? file_put_contents('temp_meta', $GLOBALS['file'].'/'.filesize('encoded_file').'/'.$id) :
                                             file_put_contents('temp_meta', $GLOBALS['tmp_meta_filename'].'/'.filesize('encoded_file').'/'.$id);

                echo " Left to send: ".CountFiles('tmp/')." tx(s)\r";
            } else {
                     echo " ERROR:\n";
                     var_dump($tx);
                     var_dump($result);
					 echo "\n End of ERROR response, Exiting.";
					 WinSleep(15);
                     exit;
            }
         /* slow down if file is big */
        //sleep(1);
        }
    }
}
//---------------------------------------------------------------------------------------------------
function DeleteTempFiles()
{
    if (is_dir('tmp/') && count(glob("tmp/*")) !== 0) {
        echo "\n Cleaning temp files...\n";

        is_file('encoded_file') ? unlink('encoded_file') : false;
        is_file('temp_meta') ? unlink('temp_meta') : false;

        $di = new RecursiveDirectoryIterator('tmp/', FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
    
        foreach ($ri as $file) {
            $file->isDir() ?  rmdir($file) : unlink($file);
        }
        return true;
    }
}
//---------------------------------------------------------------------------------------------------
function CountFiles($directory)
{
    $filecount = 0;
    $files = glob($directory."*");

    $files ? $filecount = count($files) : false;

    return $filecount;
}
//---------------------------------------------------------------------------------------------------
function Split($filename, $dir)
{
    $max = 47;
    $i   = 1;
    $r   = fopen($filename, 'r');
    $w   = fopen($dir.$i, 'w');

    while (!feof($r)) {
        $buffer = fread($r, $max);
        fwrite($w, $buffer);

        if (strlen($buffer) >= $max) {
            fclose($w);
            $i++;
            $w = fopen($dir.$i, 'w');
        }
    }
    fclose($w);
    fclose($r);
}
//---------------------------------------------------------------------------------------------------
function formatBytes($size, $precision = 0)
{
    $unit = ['Byte(s)','KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];

    for ($i = 0; $size >= 1024 && $i < count($unit)-1; $i++) {
         $size /= 1024;
    }

    return round($size, $precision).' '.$unit[$i];
}
//---------------------------------------------------------------------------------------------------
function toDec($hex)
{
    if (strlen($hex) == 1) {
        return hexdec($hex);
    } else {
             $remain = substr($hex, 0, -1);
             $last = substr($hex, -1);
             return bcadd(bcmul(16, toDec($remain)), hexdec($last));
    }
}
//---------------------------------------------------------------------------------------------------
function toHex($dec)
{
    $last = bcmod($dec, 16);
    $remain = bcdiv(bcsub($dec, $last), 16);

    if ($remain == 0) {
        return dechex($last);
    } else {
             return toHex($remain).dechex($last);
    }
}
//---------------------------------------------------------------------------------------------------
class Base91
{
    private static $chars = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
        'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!', '#', '$',
        '%', '&', '(', ')', '*', '+', ',', '.', '/', ':', ';', '<', '=',
        '>', '?', '@', '[', ']', '^', '_', '`', '{', '|', '}', '~', '"'
    );

    public static function decode($input)
    {
        if (is_array($input)) {
            $input = $input[0];
        }

        $charset = array_flip(self::$chars);

        $b = $n = $return = null;
        $len = strlen($input);
        $v = -1;
        for ($i = 0; $i < $len; ++$i) {
            $c = @$charset[$input{$i}];
            if (!isset($c)) {
                continue;
            }
            if ($v < 0) {
                $v = $c;
            } else {
                $v += $c * 91;
                $b |= $v << $n;
                $n += ($v & 8191) > 88 ? 13 : 14;
                do {
                    $return .= chr($b & 255);
                    $b >>= 8;
                    $n -= 8;
                } while ($n > 7);
                $v = -1;
            }
        }
        if ($v + 1) {
            $return .= chr(($b | $v << $n) & 255);
        }
        return $return;
    }

    public static function encode($input)
    {
        if (is_array($input)) {
            $input = $input[0];
        }

        $b = $n = $return = null;
        $len = strlen($input);
        for ($i = 0; $i < $len; ++$i) {
            $b |= ord($input{$i}) << $n;
            $n += 8;
            if ($n > 13) {
                $v = $b & 8191;
                if ($v > 88) {
                    $b >>= 13;
                    $n -= 13;
                } else {
                    $v = $b & 16383;
                    $b >>= 14;
                    $n -= 14;
                }
                $return .= self::$chars[$v % 91] . self::$chars[$v / 91];
            }
        }
        if ($n) {
            $return .= self::$chars[$b % 91];
            if ($n > 7 || $b > 90) {
                $return .= self::$chars[$b / 91];
            }
        }
        return $return;
    }
}
