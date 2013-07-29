#!/usr/bin/env python
# -*- coding: utf-8 -*-
import pandas as pd #requires numpy
#import databases
import re
import urllib
import sys
#python database dictionaries
from authors import *
from groups import *
from national import *
from fullnames import *
from issn import *
from newcitationslog import * #defines dictionay entry
from journal_alias import *
#functions
def get_authors_info(dict_ref,dict_authors,dict_groups,dict_fullnames):
    """
     The authors of the target insitutution are identified and are associated to
     a research group (using the dict_groups.py database) and an uniform full name is 
     given according to the dict_fullnames database
    """
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

def get_issn(journal=''):
    """
      For one specific journal name, their issn and Colciencias Clasification
      is obtained from the 2011 Publindex Google-Spreadsheet avalaible
      at http://fisica.udea.edu.co/fisica
    """
    if not journal: 
        return '0000-0000','00'
    
    print 'Wating to retrieve issn information for journal: %s' %journal

    gss_url="http://spreadsheets.google.com"
    gss_format="csv"
    #B: ISSN and D: CALIFICACION  
    gss_query="select B,D where (C contains '%s') order by D asc"\
            %journal.upper()
    gss_key="0AjqGPI5Q_Ez6dHV5YWY4MEdFNUs0eW1aeEpoNWJKdEE&gid=0"
    issn_url="%s/tq?tqx=out:%s&tq=%s&key=%s" %(gss_url,\
                                           gss_format,\
                                           gss_query,\
                                           gss_key)

    issn_file=urllib.urlopen(issn_url)
    jf=pd.read_csv(issn_file)
    if jf.shape[0]>0:
        #Already ordered by 'CALIFICACION'
        issn_value=jf.ix[0]['ISSN']
        category_value=jf.ix[0]['CALIFICACION']
    else:    
        issn_value='0000-0000'
        category_value='00'
    
    issn_file.close()
    return issn_value,category_value

if __name__ == '__main__':
    """
    Read an output cvs file from a Google Profile and add
    the following information 
       * Type if nacional or Internacional, according to dictionay at
             national.py
       * ISSN: First check if the issn is Already defined in the 
             dictionary at issn.py. If not obtain it from the 
             from the journal dictionary aliases dictionary at 
             journal_alias.py. Or finally try to obtain this 
             from the journal Publindex name by querying  a google 
             scholar spreadsheet with and SQL-like syntax.
       * Colciencias Publindex Clasification
       * Authors from the profile as defined in fullnames.py dictionary 
               or the others forms of the name as defined in the 
               author alias dictionary at authors.py
       * Groups at which the authors belong as defined in dictionary at 
               groups.py  
    TODO:
      * DOI

    Output cvs file under cvsfile below. 
    """
    csvfile='newcitations'
    fl = open('%slog.py' %csvfile,'a')
    fj=open('issn.py','a')
    #Initialize output (empty) pandas DataFrame
    names=['Año','Tipo','Autor(es)','Revista','Vol.','Pág.','ISSN',\
       'Artículo','Impreso','PDF','Group','DOI','Type','Proyecto ID',\
       'Autores UdeA','Clasificación Colciencias']
    df=pd.DataFrame(pd.np.nan*pd.np.ones((1,len(names))),columns=names)
    df=df.dropna()

    try:
        g=pd.read_csv('citations.csv')
        #remove phantom character from first key of the Google-Scholar profile output file
        g.columns=['Authors']+list(g.columns[1:])
        for i in range(g.shape[0]):
            auth_group,auth_institute=get_authors_info(g.ix[i],authors,groups,fullnames)
            #check if item already exists
            typepub='Internacional'
            #Convert NaN float to NaN string
            if g.ix[i]['Publication'] != g.ix[i]['Publication']:
                g['Publication'][i]=''

            logkey=g['Publication'][i].replace(' ','')+'.'+str(g.ix[i]['Volume'])+'.'+str(g.ix[i]['Pages'])
            if not entry.has_key(logkey):
                if journal_alias.has_key(g.ix[i]['Publication']):
                  #replace specific cell inside a pandas DataFrame  
                  g['Publication'][i]=journal_alias[g.ix[i]['Publication']]

                #nal o inal
                if national.has_key(g.ix[i]['Publication']): 
                  typepub='Nacional'

                #====  issn information =====
                journal=g.ix[i]['Publication']
                if not journal:
                    journal=''
                    issn[journal]=['0000-0000','00']

                if journal.upper().find('ARXIV')>=0:
                  journal='Arxiv'
                  issn[journal]=['0000-0000','00']
        #
                if not issn.has_key(journal):
                  issn_value,category_value=get_issn(journal)
                  issn[journal]=[issn_value,category_value]
                  fj.write("issn['%s']=['%s','%s']\n" %(journal,issn_value,category_value))    

                #if issn[journal][0]=='[':
                #    print i,journal,issn[journal]
                #    sys.exit()

        #DEBUG lasf field in repo program
                df=df.append({'Año':g.ix[i]['Year'],'Tipo':typepub,'Autor(es)':g.ix[i]['Authors'],\
                  'Revista':g.ix[i]['Publication'],'Vol.':g.ix[i]['Volume'],'Pág.':g.ix[i]['Pages'],\
                  'ISSN':issn[journal][0],'Artículo':g.ix[i]['Title'],'Impreso':'','PDF':'','Group':auth_group,'DOI':'','Type':'',\
                  'Proyecto ID':'','Autores UdeA':auth_institute,'Clasificación Colciencias':issn[journal][1]},ignore_index=True)

                fl.write(r"entry['%s']=True" %logkey)
                fl.write('\n')
            else:
                print 'entry:%s, already in %s.csv. Delete entry in %slog.py to uptate' %(logkey,csvfile,csvfile)
    finally:
        df.to_csv('%s.csv' %csvfile,index=False)
        fl.close()
        fj.close()
