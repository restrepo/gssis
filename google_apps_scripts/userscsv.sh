#!/usr/bin/env bash
. ./functions.sh
help="$0 HOMEDIR DOMAIN_NAME PASSWDAPPEND [FILE]
 where PASSWDAPPEND is the sequence of characters to be
 appended to the default password: user+PASSWDAPPEND.
 FILE is an optional file name to the store the $0 output.
 Examples: 
 $0 /home example.com 2011 Users.csv
 $ cat Users.csv | grep user 
   user@example.com, Name, User, user2011
  "
 
if [ "$1" == "--help" ];then
    echo "$help"
    exit
fi
if [ ! "$3" ];then 
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

createcsv $homeprof $domain $3 $4
