<?php

error_reporting(0);

require_once('lib/main.php');
require_once('config.php');

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
 @B@B@B@B@B@BBMMGG9G:              :,::::iir;rs22SXGGMMMMB'.PHP_EOL.PHP_EOL.PHP_EOL;

echo ' Lisk Send 0.3 (send file to lisk blockchain)'.PHP_EOL;
echo ' by minionsteam.org, phoenix1969, sexor, zOwn3d'.PHP_EOL;
echo ' ------------------------------------------------------'.PHP_EOL;

if (!is_dir('tmp/')) {
    mkdir('tmp/');
} else {
    if (count(glob("tmp/*")) !== 0) {
        echo ' Cleaning temp dir...'.PHP_EOL;
        DeleteTempFiles();
    }
}

if (empty($GLOBALS['ADDRESS']) or empty($GLOBALS['PASSWORD'])) {
    echo ' You need to configure config.php! Exiting.';
    sleep(5);
    die();
}

echo ' File to send : ';

while ($GLOBALS['file'] = fgets(STDIN)) {
       break;
}

$GLOBALS['file'] = trim($GLOBALS['file']);

if (is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.$GLOBALS['file'])) {
    echo PHP_EOL;
    echo ' File size         : '.formatBytes(filesize($GLOBALS['file'])).PHP_EOL;
    
    /* zip file */
    echo ' Compressing file...'.PHP_EOL;
    $zip = new ZipArchive();
    $filename = './encoded_file';
    $zip->open($filename, ZipArchive::CREATE);
    $zip->addFile($GLOBALS['file']);
    $zip->close();

    $file_content = file_get_contents('encoded_file');

    /* encode file */
    $base91 = new Base91();
    $encoded_content = $base91->encode($file_content);

    /* save to file */
    file_put_contents('encoded_file', $encoded_content);
  
    $file_name = 'encoded_file';

    echo ' Encoded file size : '.formatBytes(filesize($file_name));
    echo PHP_EOL;
    echo ' Spliting file...'.PHP_EOL;

    Split($file_name, 'tmp/');

    echo PHP_EOL;
    $cost = (0.1 * CountFiles('tmp/')) + 0.1;
    echo ' Transaction cost  : '.$cost.' lsk';
    echo PHP_EOL;
    echo ' --------------------------------------------------';
    echo PHP_EOL.PHP_EOL;
    echo ' Tx(s) to send: '.(1 + CountFiles('tmp/'));
    echo PHP_EOL.PHP_EOL;

    echo ' Proceed? (yes/no) : ';

    while ($answer = fgets(STDIN)) {
           break;
    }

    $answer = trim($answer);

    if ($answer == 'yes' xor $answer == 'y') {
        /* Send txs */
        echo PHP_EOL;
        SendData();
    } else {
             DeleteTempFiles();
    }
} else {
         echo PHP_EOL.' File does not exist or is located in another directory'.PHP_EOL;
         echo ' file to be sent must be in the same directory as tool directory, Exiting.';
         sleep(5);
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
            $data = file_get_contents($file)."'".$id;
            $tx = CreateTransaction($GLOBALS['ADDRESS'], '1', $GLOBALS['PASSWORD'], false, $data, -10);
            $id = $tx['id'];

            $result = SendTransaction(json_encode($tx), $GLOBALS['server']);

            if ($result['data']['message'] == 'Transaction(s) accepted') {
                echo ' Transaction: ('.($file).') < sent'.PHP_EOL;
//---------------------------------------------------------------------------------------------------
                /* send meta data */
                echo PHP_EOL.' Sending Meta Data...'.PHP_EOL;
 
                $size = filesize($GLOBALS['file']);
                $filename = $GLOBALS['file'];

                $Func = new Base91();
                
                /* meta data */
                $data = $Func->encode("M'".$filename."'".filesize('encoded_file')."'".$id);
            
                $tx = CreateTransaction($GLOBALS['ADDRESS'], '1', $GLOBALS['PASSWORD'], false, $data, -10);
                $result = SendTransaction(json_encode($tx), $GLOBALS['server']);
//---------------------------------------------------------------------------------------------------
                if ($result['data']['message'] == 'Transaction(s) accepted') {
                    echo ' Done. '.PHP_EOL;
                
                    /* Deleting temp files */
                    DeleteTempFiles();

                    echo PHP_EOL.' Your Data ID for file: '.$tx['id'].PHP_EOL;
                    if (!empty($GLOBALS['OS'])) {
                        echo PHP_EOL.' You can close this window...';
                        sleep(9999);
                    } else {
                             die();
                    }
                }
            } else {
                     echo 'ERROR: '.PHP_EOL;
                     var_dump($tx);
                     var_dump($result);
                     die();
            }
        } else {
            if (empty($id)) {
                /* first tx */
                $data = file_get_contents($file);
            } else {
                     /* rest tx's */
                     $data = file_get_contents($file)."'".$id;
            }
            $tx = CreateTransaction($GLOBALS['ADDRESS'], '1', $GLOBALS['PASSWORD'], false, $data, -10);
            $id = $tx['id'];

            $result = SendTransaction(json_encode($tx), $GLOBALS['server']);
            if ($result['data']['message'] == 'Transaction(s) accepted') {
                echo ' Transaction: ('.$file.') < sent'.PHP_EOL;
            } else {
                     echo 'ERROR: '.PHP_EOL;
                     var_dump($tx);
                     var_dump($result);
                     die();
            }
         /* slow down if file bigger */
        //sleep(1);
        }
    }
}
//---------------------------------------------------------------------------------------------------
function DeleteTempFiles()
{
    if (is_file('encoded_file')) {
        unlink('encoded_file');
    }

    $di = new RecursiveDirectoryIterator('tmp/', FilesystemIterator::SKIP_DOTS);
    $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
    
    foreach ($ri as $file) {
        $file->isDir() ?  rmdir($file) : unlink($file);
    }
    return true;
}
//---------------------------------------------------------------------------------------------------
function CountFiles($directory)
{
    $filecount = 0;
    $files = glob($directory."*");
    if ($files) {
        $filecount = count($files);
    }
    return $filecount;
}
//---------------------------------------------------------------------------------------------------
function Split($filename, $dir)
{
    $length = filesize($filename);
    $max = 43;
    $i   = 1;
    $r   = fopen($filename, 'r');
    $w   = fopen($dir.$i, 'w');

    while (!feof($r)) {
        $buffer =   fread($r, $max);
        fwrite($w, $buffer);

        if (strlen($buffer) >= $max) {
            fclose($w);
            $i++;
            $w  =   fopen($dir.$i, 'w');
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
