#global variables
homeprof="$1"
#delete last trailing
homeprof=$(echo $homeprof | sed -r 's/\/$//')
domain="$2"
passwdfile=/etc/passwd
#passwdfile=/home/restrepo/prog/google_apps_accounts/gssis/google_apps_scripts/passwd
listusers=$(cat $passwdfile |grep -v "/bin/false"| grep ":$homeprof" | awk -F":" '{print $1}' |sort |uniq )
function forwardcm {
    for i in $listusers
    do 
	realhome=$(cat $passwdfile | grep ":$homeprof" |grep -E "^$i:" | awk -F":" '{print $6}' |sort |uniq )
        if [ -f "$realhome/.forward" ];then 
           #add redirection to google
            existe=$(cat "$realhome/.forward" | grep "$malias")
            if [ ! "$existe" ];then
      		echo adding ${i}@$malias to $i/.forward
      		echo -e "\n${i}@$malias">>"$realhome/.forward" 
            else
		echo ".forward alredy configured for $i"
            fi
        else
           #create .forward with redirection to google
            echo .forward created in $i
            echo "${i}@$domain">"$realhome/.forward" 
            echo "${i}@$malias">>"$realhome/.forward" 
        fi
        #change permissions
        owner=$(/bin/ls -l "$realhome/.forward" | awk '{print $3}')
        if [ "$owner" = root ]; then 
      	    group=$(/bin/ls -ld $realhome | awk '{print $4}')
      	    chown ${i}:$group "$realhome/.forward" 
        fi
    done
}
function checkforwards {
    for i in $listusers
    do 
	realhome=$(cat $passwdfile | grep ":$homeprof" |grep -E "^$i:" | awk -F":" '{print $6}' |sort |uniq )
	existe=$(cat "$realhome/.forward" 2>/dev/null | grep "$malias")
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
    echo -n Processing >&2
    for i in $listusers
    do 
	#check for uniq entries, delete quotes: "", in full name
	fullname=$(cat $passwdfile | grep $homeprof | grep $i: | awk -F":" '{print $5}' | awk -F"," '{print $1}' | sort |uniq | sed 's/\"//g')
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
	elif [ "$(nameparts $fullname)" > 4 ];then 
	    namesfull=$(nameparts $fullname)
	    totalname=$(echo $fullname |\
                 sed -r 's/([a-zA-Z0-9]*\ [a-zA-Z0-9]*\ )/\1:/')
	    firstname=$(echo $totalname | awk -F":" '{print $1}') 
	    lastname=$(echo $totalname | awk -F":" '{print $2}')
	else 
	    firstname=$fullname
	    lastname=""
	    echo "fix $fullname in $file" >&2
	fi
	echo "${i}@$domain ,$firstname ,$lastname ,${i}$appendpasswd" >> $tmpfile
	echo -n $j. >&2
    done
    echo ""
    report=$(grep -E ",\s*," $tmpfile)
    if [ "$report" ];then
	echo "The following lines have format problems and will not be processed:"  >&2
	echo "$report"
    fi 

    echo "==========CSV FILE ===========================" >&2
    if [ "$file" ];then 
	cat $tmpfile > $file
    else
	cat $tmpfile
    fi
    /bin/rm -f $tmpfile
}
