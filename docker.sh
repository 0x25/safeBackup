#!/bin/bash

# var
localVolumePath='/mnt/Vault'
listenPort=8080

# create docker IMG
echo -e "\e[32m>> CREATE DOCKER IMAGE\e[0m"
docker build -t safebackup .
echo ""

# start docker IMG in background
echo -e "\e[32m>> START SAFEBACKUP CONTAINER\e[0m"
docker run -d --rm -it -v ${localVolumePath}:/var/www/html/uploads -p ${listenPort}:80 --name safebackup safebackup
echo ""

# copy public key in local volume path
if [ "$EUID" -ne 0 ]; then 

  echo ""
  echo "--------------------------------------"
  echo -e "\e[31mPlease copy public.pem (run as root)"
  echo -e "# sudo cp public.pem ${localVolumePath}\e[0m"
  echo "--------------------------------------"
  echo ""
else
  cp public.pem ${localVolumePath}
fi

echo -e "\e[32m>> LIST CONTAINER\e[0m"
docker ps -a --filter "name=safebackup"

echo ""
echo -e "\e[32m>> CURL ACCESS\e[0m"
sleep 4
curl -I http://127.0.0.1:${listenPort}


