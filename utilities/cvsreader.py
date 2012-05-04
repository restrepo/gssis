#!/usr/bin/env python
# -*- coding: utf-8 -*-
#From http://www.doughellmann.com/PyMOTW/csv/
import csv
import sys
from authors import *
from groups import *
from national import *
import re
def get_authors_info(dict_ref,dict_authors,dict_groups):
    #initalize strings
    auth_institute=''
    auth_group=''
    #==authors from the Institution===
    xauthor=[]
    author_id=-1
    for auth in (dict_ref['Authors']).split(';'):
      auth=re.sub(r'^\s+','',auth)
      if dict_authors.has_key(auth):
          author_id=dict_authors[auth]
          xauthor.append(author_id)

    if not xauthor: xauthor.append(-1)
    #===================================
    for i in xauthor:
        if int(i)>-1:
            if not dict_groups[i] in auth_group.split('; '):
                auth_group=auth_group+dict_groups[i]
            if i!=xauthor[-1]:
                auth_group=auth_group+'; '
        else:
            auth_group='Null'

    return auth_group,auth_institute


if __name__ == '__main__':
    #generic open
    #f = open(sys.argv[1], 'rt')
    #specific open
    f = open('citations_bak.csv', 'rt')
    fw = open('newcitations.csv','w')
    csv_writer=csv.writer(fw)
    csv_writer.writerow(['Año','Tipo','Autor(es)','Revista','Vol.','Group'])
    try:
        reader = csv.DictReader(f)
        #fix Author fieldname
        reader.fieldnames[0]='Authors'
        #Prepare backup rows inside list x:
        x=[]
        for row in reader:
            x.append(row)
            typepub='Internacional'
            auth_group,auth_institute=get_authors_info(row,authors,groups)
            #nal o inal
            if national.has_key(row['Publication']): typepub='Nacional'
            csv_writer.writerow([row['Year'],typepub,row['Authors'],row['Publication'],\
                                 row['Volume'],auth_group])
            print row
    finally:
        f.close()
        fw.close()
