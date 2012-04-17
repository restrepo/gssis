#!/usr/bin/env bash
. ./functions.sh
help="$0 HOMEDIR DOMAIN_NAME [check]
 where check is an optional parameter
 Examples: 
 * To delete .forward's use:
   $0 /home example.com
 * To check the .forwards use:
   $0 /home example.com check"
 
if [ "$1" == "--help" ];then
    echo "$help"
    exit
fi
if [ ! "$2" ];then 
    echo "USAGE:"
    echo "$help"
    exit
fi

homeprof="$1"
domain="$2"
malias="${domain}.test-google-a.com"
if [ ! -d $homeprof ]; then
    echo "Error: home no existe"
    exit
fi

#checkforwards
if [ "$3" ];then 
    if [ "$3" == "check" ];then 
	checkforwards $homeprof $domain
    else
	echo "USAGE:"
	echo "$help"
    fi
    exit
fi

#delete or modify .forward's
forwardlocaldel $homeprof $domain

