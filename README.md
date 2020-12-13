# safeBackup V0.1 ^^ (PoC)
Web drag and drop interface to backup files with asymmetric key

<p align="center">
<img src="https://github.com/0x25/safeBackup/blob/master/safebackup.PNG?raw=true" alt="SafeBackup">
</p>

# Purpose
Easily save files with drag and drop website.
All file are encrypted with asymmetric algorithm.

# Install

 1. Generate RSA pem file and extract public.pem (see upload.php file)
 2. Save private.pem in safe place (other sever/keepass...)
 3. Copy repo files in /var/www(/html)
 4. Install and configure webserver (apache/nginx ...) or run php -S 0.0.0.0:8080

# Files

 - index.php is drag and drop files backup page 
 - upload.php is php file who encrypt data with public key and save file in uploads directory
 - restore.php is a php script to restore file with private key

<p align="center">
<img src="https://github.com/0x25/safeBackup/blob/master/safeBackup.gif?raw=true" alt="SafeBackup">
</p>

# Encrypted file format
each encrypted file contains on first line the envelope key in base64 and on the second line base64 encrypted data.

# Recover example
```
chmod +x restore.php
./restore.php -f uploads/myfiletorestore.123123123123
```
More parameters are available with -h option
```
-k [priv.pem] private pem key
-h for show Help
-f set the path/name of file to recover
-p set the private pem key passphrase if needed
```

# Docker
## Build the image
```
docker build --tag safebackup .
```

## Launch the container to upload data to a specific folder containing public.pem
```
docker run --rm -it -v /path/to/vault/:/uploads -p 8080:80 safebackup
```

## Shell aliases
```
alias docker-vault="docker run --rm -it -v /path/to/vault/:/uploads -p 8080:80 safebackup"
``
