<?php

/*

Create RSA KEY
// create cert
openssl genrsa -des3 -out private.pem 2048
// export pubic cert
openssl rsa -in private.pem -outform PEM -pubout -out public.pem
// remove password on private key (save it safe)
openssl rsa -in private.pem -out priv.pem

update php.ini to download big file
 post_max_size 
 upload_max_filesize

*/


/** CONFIG PARMAS **/
$ds = DIRECTORY_SEPARATOR;
$pubKey = 'uploads/public.pem';
$storeFolder = 'uploads';

/** FUNC **/
function debug($logMessage,$logFile='out.txt'){
    file_put_contents($logFile,'SafeBAckup - '.$logMessage."\n",FILE_APPEND);
}

/** CODE **/

//check if pubKey exist
$cert = file_get_contents($pubKey);
if( $cert === false){
	debug('ERROR : Fail to load RSA cert; check cert path');
	exit(1);
}
else{
	debug('INFO : pubKey found');
}

$pk1 = openssl_get_publickey($cert);
//check load of pubKey
if($pk1 === false){
	debug('ERROR : Fail to extract pub cert; check cert format');
  exit(2);
}

//check files
if (!empty($_FILES)) {
	//check upload error
	if($_FILES['file']['error'] !== 0){
	  debug('ERROR : Load file error ['.$_FILES['file']['error']);
  	exit(3);
	}

    $mktime = str_replace('.','',microtime(true)); 	//generate uniq value to suffixe file; maybe not the best idea
    $data = file_get_contents($_FILES['file']['tmp_name']);
    unset($_FILES['file']['tmp_name']);

    //$targetPath = dirname( __FILE__ ) . $ds . $storeFolder . $ds;
    $targetPath = $storeFolder . $ds;
    $fileName = $_FILES['file']['name'].'.'.$mktime; //create uniq file name
    $targetFile =  $targetPath.$fileName;

		debug('INFO : data length '.strlen($data));
		debug('INFO : targetFile '.$targetFile);

	  $res = openssl_seal($data, $sealed, $ekeys, array($pk1));

	if($res === false){
		debug('ERROR : Fail seal data');
		exit(4);
	}

	debug('eKey '.base64_encode($ekeys[0]));
	debug('$seled '.base64_encode($sealed));

	$res1 = file_put_contents($targetFile,base64_encode($ekeys[0])."\r\n");
	$res2 = file_put_contents($targetFile,base64_encode($sealed),FILE_APPEND);

  if( $res1 ===false or $res2 === false){
		debug('ERROR : Writing encrypted file');
	}

	openssl_free_key($pk1);
}


?>
