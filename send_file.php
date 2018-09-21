<?php

error_reporting(0);
require_once('lib/main.php');
require_once('config.php');

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    chdir('../');
    $GLOBALS['OS'] = 'WIN';
}

echo '
                                                   F                      
                                                  q@B                     
                                                 u@B@O                    
                                                7@BBB@X                   
                                               7@BBOMB@u                  
                                               8B@MMOMB@7                 
                                                OB@MMOMB@i                
                                             E   BBBMMOBB@,               
                                            @BX   @BBOMOBB@               
                                           OB@BY   @BBOMMB@@              
                                          NB@M@@r  .@@BMMMBB@             
                                         uB@MMM@Br  :@BMOMM@BM            
                                        7@@MMOBB@O   r@BMOMM@B0           
                                       iB@MMOMB@O     j@BMOMO@B2          
                                      :B@MMOMM@B       S@BMOMM@Bv         
                                      B@MMOMM@B         G@BMOMM@@i        
                                     B@MMOMM@B           M@MMOMM@B:       
                                    B@MMOMM@B,            @@MMOMM@B.      
                                   8@BMOMM@@i              @@MMOMM@B      
                                  X@BMOMM@B7               .B@MMOMM@B     
                                 u@BMOMO@Bu                 :B@MMOMB@O    
                                7@BMOMM@BP                   7B@MMOMB@q   
                               i@@BOMMBBO                     jB@MMOMB@2  
                              ,@@MOMOMBE                       JBMOMOMB@L 
                             :@BBMMOMM@                         @BMOMMBB@J
                             rB@BBOMOMB@:                     .MBMOMOMB@B1
                               8@@BOMOMB@F                   7@BBMMOMB@B, 
                                L@B@MMMMB@B.                E@BMMMMBB@P   
                                 .@B@MMOMB@@7             :B@BMOMM@@@r    
                                   NB@MMOMM@B0vJJuuqZ,   JB@MMOMM@B@      
                                    7B@MMMMM@B@B@B@B7   MB@MMOMM@@F       
                                     .@@BMOMMMMBB@Z   :@B@MMOBB@@i        
                    ......,.,...       F@BBMMOMM@B. .u@B@MBMBB@M          
                .,,,.........,.,,:..    i@B@MBMMZGrMB@B@B@@@B@u           
             .,:,.... . ... ......,,:.    M@@OZU1P@@M2F25255X:            
           .::...        ,,:...  ......    rSBEPPMB5                      
          ,,...  .iLSkkuv::ir;,,.     .,.     MOMB2                       
           ... .:1O8j7ii7jvi. ,ELLXkFF25ku   jBBBP
     ,r7v;:.,,,i5Mu.      :Li :uPv@BBZqkXNv  rOB@7
  .iuGZkuuJJ7:iLEk    7u:  :jirXGrX@PUv7i:,   .r. 
 .7FMj:     :LLrOi   SBBB.  YJ7jMUri,.,.,.,  .::
 7JML     7i .v;5i   jZSL   LFi77Fi:,::::::. :i:
,75E.   :S@Bv r7rr         :57r7Y7::::i::::,:,r 
,vuP    .8Zki iYrrr.      ,J;vFSLi::::::::.:,:i 
 r7U,     .   vr,:r7i,..,iv:iPSLi:::::::i7J:,i. 
 ,7v7.       i7:vi.:iriiiiiJuLri::::,,i5E@M:.i  
  ,rvvi.. ..ri,u@EY;i:::i7L7rri::::.:uZMMM7 ::  
   .:r77riii:ij5r::::iii::.;YSi::,,7Z@8Fvr.,:u. 
      ,,::::rri.....,..7jLkB@P,:,:2GBZ7.,,,::i. 
      i::::::FLv;rjMZqG@B@B@O...iXOOFi.,,::i:,  
      L:.,,.:O@B@B@B@B@B@B@L  .7EZZL:.:::::i::  
      jJ ..,. :1M@B@B@BOu:   .JO8ki..::::::i::  
      ,qi         ...    .:rjSOqF:.,::::::::::. 
       YS:.,,i:i:;i;r7u5S0SJE@81...::::::::::i. 
       .u8P2XSSSFFqPkSPFFuuJu2q5,.,,::::::::::, 
       ..S85U2jU21U2UujuUuJ1uU5Sr..:::::::::,:. 
       ...UuuJJLYv7;vvYLu2U1FU251:....,,,::::r7 
       ...vUJYYuLv7LvJvLYSLuuuJ1jv::ir;7vYLUUXB,
       .:.ruJLLJJLjLuujJF2JLjuuuSuqUXkkjqNkF5SF 
       .7J7uvYYju1uF55U5UjujLjYuSuuNFPSY1qPkFP. 
        :uuvLLJLuu2UuLjjUuuYU1U7LvYuS11ukXXNGi  
      .GkFrLjUYJLjYuvvuUYuuuUFu2Juu1215NNGON,   
       E@BF7JUFSXSF21JFFFFS151kXPkF5q0MM@Ov     
       2BM@MFUF0GONX55u150OOZqkPkNqMB@85:       
       J@OOB@B@: :8O0PN0BqjLX@OGOO@Mi           
        E@@@BG    :BB@B@M    @BO8MO             
         :S@@7   JEB@B@BJ    .@B@Bv             
                0@@@B@B@@vii:EB@B@BZ'.PHP_EOL;

if (!is_dir('tmp/')) {
    mkdir('tmp/');
} else {
         DeleteTempFiles();
}

echo PHP_EOL;
echo ' Send file to Lisk blockchain, by minionsteam.';
echo PHP_EOL;
echo ' --------------------------------------------------';
echo PHP_EOL;
echo PHP_EOL;

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

if (is_file($GLOBALS['file'])) {
    echo PHP_EOL;
    echo ' File size         : '.filesize($GLOBALS['file']).'b'.PHP_EOL;

    $file_content = file_get_contents($GLOBALS['file']);

    $base91 = new Base91();
    $encoded_content = $base91->encode($file_content);

    /* save to file */
    file_put_contents('encoded_file', $encoded_content);
  
    $file_name = 'encoded_file';

    echo ' Encoded file size : '.filesize($file_name).'b';
    echo PHP_EOL;

    Split($file_name, 'tmp/');

    echo PHP_EOL;
    $cost = (0.1 * CountFiles('tmp/')) + 0.1;
    echo ' Transaction cost  : '.$cost.' lsk';
    echo PHP_EOL;
    echo ' --------------------------------------------------';
    echo PHP_EOL.PHP_EOL;
    echo ' Tx(s) to send: '.(1 + CountFiles('tmp/'));
    echo PHP_EOL.PHP_EOL;

    /* Send txs */
    SendData();
} else {
         echo ' Cannot find file! Exiting.';
         sleep(3);
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
                echo ' Transaction: ('.($key+1).') < sent'.PHP_EOL;
//---------------------------------------------------------------------------------------------------
                /* send meta data */
                echo PHP_EOL.' Sending Meta Data...'.PHP_EOL;
 
                $size = filesize($GLOBALS['file']);
                $filename = $GLOBALS['file'];

                $Func = new Base91();
                
                /* meta data */
                $data = $Func->encode("M'".$filename."'".$size."'".$id);
            
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
                echo ' Transaction: ('.($key+1).') < sent'.PHP_EOL;
            } else {
                     echo 'ERROR: '.PHP_EOL;
                     var_dump($tx);
                     var_dump($result);
                     die();
            }
        //sleep(1);
        }
    }
}
//---------------------------------------------------------------------------------------------------
function DeleteTempFiles()
{
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

    /* delete temp */
    unlink('encoded_file');
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
