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


def read_google_cvs(gss_url="http://spreadsheets.google.com",\
    gss_format="csv",\
    gss_key="0AuLa_xuSIEvxdERYSGVQWDBTX1NCN19QMXVpb0lhWXc",\
    gss_sheet=0,\
    gss_query="select B,D,E,F,I where (H contains 'GFIF') order by D desc",\
    gss_keep_default_na=False
    ):
    import urllib
    import pandas as pd
    """
    read a google spreadsheet in cvs format and return a pandas DataFrame object.
       ....
       gss_keep_default_na: (False) Blank values are filled with NaN
    """
    issn_url="%s/tq?tqx=out:%s&tq=%s&key=%s&gid=%s" %(gss_url,\
                                           gss_format,\
                                           gss_query,\
                                           gss_key,\
                                           str(gss_sheet))

    gfile=urllib.urlopen(issn_url)
    return pd.read_csv(gfile,keep_default_na=gss_keep_default_na)

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
    fj=open('issn.py','a')
    #Initialize output (empty) pandas DataFrame
    names=['Año','Tipo','Autor(es)','Revista','Vol.','Pág.','ISSN',\
       'Artículo','Impreso','PDF','Group','DOI','Type','Proyecto ID',\
       'Autores UdeA','Clasificación Colciencias']
    df=pd.DataFrame(pd.np.nan*pd.np.ones((1,len(names))),columns=names)
    df=df.dropna()

    try:
        g=pd.read_csv('citations.csv')
        print('Loading publindex data base ...')
        publindex=read_google_cvs(gss_key='0AjqGPI5Q_Ez6dHV5YWY4MEdFNUs0eW1aeEpoNWJKdEE',gss_query="select *")
        print 'Publindex loaded:',publindex.columns
        #remove phantom character from first key of the Google-Scholar profile output file
        g.columns=['Authors']+list(g.columns[1:])
        for i in range(g.shape[0]):
            auth_group,auth_institute=get_authors_info(g.ix[i],authors,groups,fullnames)
            #check if item already exists
            typepub='Internacional'
            #Convert NaN float to NaN string
            if g.ix[i]['Publication'] != g.ix[i]['Publication']:
                g['Publication'][i]=''

            #logkey=g['Publication'][i].replace(' ','')+'.'+str(g.ix[i]['Volume'])+'.'+str(g.ix[i]['Pages'])
            if True: #not entry.has_key(logkey):
                if journal_alias.has_key(g.ix[i]['Publication']):
                  #replace specific cell inside a pandas DataFrame  
                  g['Publication'][i]=journal_alias[g.ix[i]['Publication']]

                #TODO: Colciencias Specific column: move to colciencias plugin
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
                #TODO: Category: Colciencias Specific column: move to colciencias plugin        
                #      obtain ISSN from crossref
                if not issn.has_key(journal):
                  jf=publindex[publindex['NOMBRE'].str.contains(journal.upper())].sort(['CALIFICACION'],ascending=True).reset_index(drop=True)
                  #TODO: Need to be rewritten: 
                  #ISSN must be obtained from crossref 
                  #ISSN required to obtain Impact Factor
                  if jf.shape[0]>0:
                      issn_value=jf.ix[0]['ISSN']
                      category_value=jf.ix[0]['CALIFICACION']
                  else:    
                      issn_value='0000-0000'
                      category_value='00'
                            
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

    finally:
        df.to_csv('%s.csv' %csvfile,index=False)
        fj.close()
