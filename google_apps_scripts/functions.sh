#global variables
homeprof="$1"
domain="$2"
passwdfile=/etc/passwd
#passwdfile=/home/restrepo/prog/google_apps_accounts/gssis/google_apps_scripts/passwd
listusers=$(cat $passwdfile |grep -v "/bin/false"| grep ":$homeprof" |sort |uniq | awk -F":" '{print $1}')
function forwardcm {
    for i in $listusers
    do 
        if [ -f "$homeprof/$i/.forward" ];then 
           #add redirection to google
            existe=$(cat "$homeprof/$i/.forward" | grep "$malias")
            if [ ! "$existe" ];then
      		echo adding ${i}@$malias to $i/.forward
      		echo -e "\n${i}@$malias">>"$homeprof/$i/.forward" 
            else
		echo ".forward alredy configured for $i"
            fi
        else
           #create .forward with redirection to google
            echo .forward created in $i
            echo "${i}@$domain">"$homeprof/$i/.forward" 
            echo "${i}@$malias">>"$homeprof/$i/.forward" 
        fi
        #change permissions
        owner=$(/bin/ls -l "$homeprof/$i/.forward" | awk '{print $3}')
        if [ "$owner" = root ]; then 
      	    group=$(/bin/ls -ld $homeprof/$i | awk '{print $4}')
      	    chown ${i}:$group "$homeprof/$i/.forward" 
        fi
    done
}
function checkforwards {
    for i in $listusers
    do 
	existe=$(cat "$homeprof/$i/.forward" 2>/dev/null | grep "$malias")
	if [ "$existe" ]; then
	    echo $existe : OK!
	else 
	    echo "============================================="
	    echo "Error: $i no tiene .forward o mal configurado"
	    echo "============================================="
	fi
    done
}
function nameparts { echo $#;}
function createcsv {
    tmpfile=/tmp/tmpcsv$(date +%s)
    appendpasswd=$3
    if [ "$4" ]; then 
	file=$4
    fi
    echo "email address ,first name ,last name ,password" > $tmpfile
    for i in $listusers
    do 
	#check for uniq entries, delete quotes: "", in full name
	fullname=$(cat $passwdfile | grep $homeprof | grep $i | uniq | sed 's/"//g'| awk -F":" '{print $5}' | awk -F"," '{print $1}')
	if [ "$(nameparts $fullname)" == 0 ];then 
	    firstname=""
	    lastname=""
	elif [ "$(nameparts $fullname)" == 1 ];then 
	    firstname=$(echo $fullname | awk '{print $1}') 
	    lastname=""
	elif [ "$(nameparts $fullname)" == 2 ];then 
	    firstname=$(echo $fullname | awk '{print $1}') 
	    lastname=$(echo $fullname | awk '{print $2}') 
	elif [ "$(nameparts $fullname)" == 3 ];then 
	    firstname=$(echo $fullname | awk '{print $1" "$2}') 
	    lastname=$(echo $fullname | awk '{print $3}') 
	elif [ "$(nameparts $fullname)" == 4 ];then 
	    firstname=$(echo $fullname | awk '{print $1" "$2}') 
	    lastname=$(echo $fullname | awk '{print $3" "$4}')
	elif [ "$(nameparts $fullname)" == 5 ];then 
	    firstname=$(echo $fullname | awk '{print $1" "$2" "$3}') 
	    lastname=$(echo $fullname | awk '{print $4" "$5}')
	else 
	    firstname=$fullname
	    lastname=""
	    echo "fix $fullname in $file" >&2
	fi
	echo "${i}@$domain ,$firstname ,$lastname ,${i}$appendpasswd" >> $tmpfile

    done
    if [ "$file" ];then 
	cat $tmpfile > $file
    else
	cat $tmpfile
    fi
    /bin/rm -f $tmpfile
}
