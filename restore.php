#!/usr/bin/env php
<?php

/**
Script to restore file
php -f restore.php
**/

/** CONFIG **/
$privKey = 'priv.pem'; //private pem
$recoverPath = 'recover';


/** CODE **/


function help(){
	echo "UploadSec recover help: \n";
	echo "-k [priv.pem] private pem key\n";
   	echo "-h for show Help\n";
    echo "-f set a name of file to recover\n";
    echo "-p set private pem key passphrase\n";
	exit(0);
}

$fileName = '';
$passPhrase = '';

//paramscript
if(php_sapi_name() === 'cli'){
        $options = getopt("f:hk::p:");
        if( count($options) === 0 ){
        	help();
        }

        if(isset($options['h'])){
        	help();
        }

        if( isset($options['k']) ){
        	$privKey = $options['k'];
        }

        if( isset($options['f']) ){
        	$fileName = $options['f'];
        }

        if( isset($options['p']) ){
        	$passPhrase = $options['p'];
        }
}

$pem = file_get_contents($privKey);
if($pem === false){
	echo "ERROR : Fail open priv key; check path\n";
	exit(1);
}

$pkeyid = openssl_get_privatekey($pem,$passPhrase);
if($pkeyid === false){
	echo "ERROR : Fail load priv key; check format or passPhrase\n";
	exit(2);
}

if (!file_exists($fileName)){
	echo "ERROR : Fail to read file\n";
	exit(3);
}

echo "INFO : try restore $fileName \n";

$file = file($fileName, FILE_IGNORE_NEW_LINES);

$eKey = $file[0];
$datas = $file[1];
$name = pathinfo($fileName, PATHINFO_FILENAME);

echo "Info : EKey is [$eKey]\n";
echo "Info : Data length [".strlen($datas)."]\n";
echo "INFO : Restore file in $recoverPath/$name\n";

//get encrypted content
$sealed = base64_decode($datas);
//get envelope key
$env_key = base64_decode($eKey);

// decrypte $sealed/data to $open en put result in /recover
@mkdir($recoverPath);
if(!is_dir($recoverPath)){
	echo "ERROR : Fail to create recover directory\n";
	exit(4);
}

if (openssl_open($sealed, $open, $env_key, $pkeyid)) {
    file_put_contents($recoverPath.'/'.$name, $open);
    echo "Done\n";
} else {
	echo "ERROR : Fail to decrypt datas\n";
	exit(5);
}

openssl_free_key($pkeyid);

?>