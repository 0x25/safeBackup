#!/bin/bash

id=$(docker inspect --format="{{.Id}}" safebackup)
echo -e "\e[32m>> STOP CONTAINER\e[0m"
docker stop $id

echo -e "\e[32m>> LIST SAFEBACKUP CONTAINER\e[0m"
docker ps -a --filter "name=safebackup"

echo -e "\e[32m>> CLEAR IMAGE CONTAINER\e[0m"
docker rmi safebackup

echo -e "\e[32m>> LIST ALL IMAGES\e[0m"
docker image ls

