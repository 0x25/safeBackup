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
$pubKey = '/uploads/public.pem';
$storeFolder = '/uploads';
$log = 'out.txt';

/** CODE **/

//check if pubKey exist
$cert = file_get_contents($pubKey);
if( $cert === false){
	file_put_contents($log,'ERROR : Fail to load RSA cert; check cert path'."\n",FILE_APPEND);
	exit(1);	
}

$pk1 = openssl_get_publickey($cert);
//check load of pubKey
if($pk1 === false){
	file_put_contents($log,'ERROR : Fail to extract pub cert; check cert format'."\n",FILE_APPEND);
	exit(2);	
}

//check files
if (!empty($_FILES)) {
	//check upload error
	if($_FILES['file']['error'] !== 0){
		file_put_contents($log,'ERROR : Load file error ['.$_FILES['file']['error']."]\n",FILE_APPEND);
		exit(3);
	}

    $mktime = str_replace('.','',microtime(true)); 	//generate uniq value to suffixe file; maybe not the best idea
    $data = file_get_contents($_FILES['file']['tmp_name']);            
    unset($_FILES['file']['tmp_name']);

    //$targetPath = dirname( __FILE__ ) . $ds . $storeFolder . $ds;
    $targetPath = $storeFolder . $ds;
    $fileName = $_FILES['file']['name'].'.'.$mktime; //create uniq file name
    $targetFile =  $targetPath.$fileName;
 
    file_put_contents($log,'INFO : data length '.strlen($data)."\n",FILE_APPEND);
	file_put_contents($log,'INFO : targetFile '.$targetFile."\n",FILE_APPEND);
    
	$res = openssl_seal($data, $sealed, $ekeys, array($pk1));

	if($res === false){
		file_put_contents($log,'ERROR : Fail seal data');
		exit(4);	
	}

	file_put_contents($targetFile,base64_encode($ekeys[0])."\r\n");
	file_put_contents($targetFile,base64_encode($sealed),FILE_APPEND);

	openssl_free_key($pk1);
}


?>
