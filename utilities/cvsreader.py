#!/usr/bin/env python
# -*- coding: utf-8 -*-
#From http://www.doughellmann.com/PyMOTW/csv/
import csv
import sys
import re
import urllib
from os import path
#python database dictionaries
from authors import *
from groups import *
from national import *
from fullnames import *
from issn import *
from newcitationslog import *
from journal_alias import *

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

def get_issn(journal):
    print 'Wating to retrieve issn information for journal: %s' %journal
    gss_url="http://spreadsheets.google.com"
    gss_format="csv"
    gss_query="select B,D where (C contains '%s') order by D asc"\
                %journal.upper()
    gss_key="0AjqGPI5Q_Ez6dHV5YWY4MEdFNUs0eW1aeEpoNWJKdEE&gid=0"
    issn_url="%s/tq?tqx=out:%s&tq=%s&key=%s" %(gss_url,\
                                               gss_format,\
                                               gss_query,\
                                               gss_key)

    issn_file=urllib.urlopen(issn_url)
    issn_string=issn_file.read()
    #The value is entry A2 of cvs; 0,1 in python notation:
    if (issn_string.split('\n')[0]).upper().find('ERROR:')==-1 and issn_string.split('\n')[1]:
        issn_value=eval(issn_string.split('\n')[1].split(',')[0])
    else:
        issn_value='0000-0000'

    issn_file.close()
    return issn_value

if __name__ == '__main__':
    #generic open
    #f = open(sys.argv[1], 'rt')
    #specific open
    csvfile='newcitations'
    f = open('citations.csv', 'rt')
    fw = open('%s.csv' %csvfile,'w')
    fl = open('%slog.py' %csvfile,'a')
    fj=open('issn.py','a')

    
    csv_writer=csv.writer(fw)
    csv_writer.writerow(['Año','Tipo','Autor(es)','Revista','Vol.','Pág.','ISSN',\
                         'Artículo','Impreso','PDF','Group','DOI','Tipo','Proyecto ID',\
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
            #check if item already exists
            logkey=row['Publication'].replace(' ','')+'.'+row['Volume']+'.'+row['Pages']
            if not entry.has_key(logkey):
                #Publindex journal Name
                if journal_alias.has_key(row['Publication']):
                    row['Publication']=journal_alias[row['Publication']]
                #nal o inal
                if national.has_key(row['Publication']): typepub='Nacional'
                #====  issn information =====
                journal=row['Publication']
                #Arxiv has not issn:
                if journal.upper().find('ARXIV')>=0:
                    journal='Arxiv'
                    issn[journal]='0000-0000'

                if not issn.has_key(journal):
                    issn_value=get_issn(journal)
                    issn[journal]=issn_value
                    fj.write("issn['%s']='%s'\n" %(journal,issn_value))

                #===================
                    
                        
                    



                csv_writer.writerow([row['Year'],typepub,row['Authors'],row['Publication'],\
                                 row['Volume'],row['Pages'],issn[journal],row['Title'],'','',\
                                 auth_group,'','','',auth_institute])
                print row
                fl.write(r"entry['%s']=True" %logkey)
                fl.write('\n')

            else:
                print 'entry:%s, already in %s.csv. Delete entry in %slog.py to uptate' %(logkey,csvfile,csvfile)
    finally:
        f.close()
        fw.close()
        fl.close()
        fj.close()
