#!/usr/bin/env python
# -*- coding: utf-8 -*-
#From http://www.doughellmann.com/PyMOTW/csv/
import csv
import sys
import re
#python database dictionaries
from authors import *
from groups import *
from national import *
from fullnames import *

def get_authors_info(dict_ref,dict_authors,dict_groups,dict_fullnames):
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
            #fill group of article
            if not dict_groups[i] in auth_group.split('; '):
                auth_group=auth_group+dict_groups[i]+'; '
            #fill insitutional Authors Full Names
            auth_institute=auth_institute+dict_fullnames[i]+'; '
        else:
            auth_group=''
            auth_institute=''
    
    #remove the last; '; '
    auth_group=re.sub(r'; $','',auth_group)
    auth_institute=re.sub(r'; $','',auth_institute)
    return auth_group,auth_institute


if __name__ == '__main__':
    #generic open
    #f = open(sys.argv[1], 'rt')
    #specific open
    f = open('citations_bak.csv', 'rt')
    fw = open('newcitations.csv','w')
    csv_writer=csv.writer(fw)
    csv_writer.writerow(['Año','Tipo','Autor(es)','Revista','Vol.','Pág.','ISSN',\
                         'Artículo','Impreso','PDF','Group','Tipo','Proyecto ID',\
                         'Autores UdeA'])
    try:
        reader = csv.DictReader(f)
        #fix Author fieldname
        reader.fieldnames[0]='Authors'
        #Prepare backup rows inside list x:
        x=[]
        for row in reader:
            x.append(row)
            typepub='Internacional'
            auth_group,auth_institute=get_authors_info(row,authors,groups,fullnames)
            #nal o inal
            if national.has_key(row['Publication']): typepub='Nacional'
            csv_writer.writerow([row['Year'],typepub,row['Authors'],row['Publication'],\
                                 row['Volume'],row['Pages'],'',row['Title'],'','',\
                                 auth_group,'','',auth_institute])
            print row
    finally:
        f.close()
        fw.close()
