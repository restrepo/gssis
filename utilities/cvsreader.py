#!/usr/bin/env python
# -*- coding: utf-8 -*-
import pandas as pd
import urllib
#It recommended to use http://python-requests.org instead
from pygoogle import pygoogle #From: https://code.google.com/p/pygoogle/
from bs4 import BeautifulSoup
import httplib
import re #, sys, cgi
import sys
#python database dictionaries
from issn import *
from newcitationslog import * #defines dictionay entry
from journal_alias import *
pd.set_option('display.max_rows', 800)
from InsitutoFisicaUdea import *

#functions

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

def catutf8(fileutf8):
    """
    Print UTF-8 file.
    """
    fo=open(fileutf8,'r')
    a=fo.read()
    a=a.decode('utf8')
    fo.close()
    print a
    
def get_impact_factor_from_issn(issn='1475-7516',debug=False):
    '''
      For the input ISSN in the format NNNN-NNNN obtain
      the headers and the datasets in a nested list
      equivalent to an array of (# headers)*[4 (years)]
    '''
    g = pygoogle('site:http://www.bioxbio.com/if/html '+issn)
    g.pages = 1
    if g.get_urls():
        if_file=urllib.urlopen(g.get_urls()[0])
        html=if_file.read()
        if_file.close()
    else:
        return [],[]

    if debug: print(html)
    soup = BeautifulSoup(html)
    table = soup.find("table")

    # The first tr contains the field names.
    headings = [th.get_text().strip() for th in table.find("tr").find_all("td")]

    datasets = []
    for row in table.find_all("tr")[1:]:
        dataset = [eval(td.get_text().replace('-','0')) for td in row.find_all("td")]
        datasets.append(dataset)
        
    return headings,datasets

def getIF(issn='1475-7516'):
    h,c=get_impact_factor_from_issn(issn)
    if h:
        return pd.DataFrame(c,columns=h)
    else:
        return []
    
#Adapted from http://tex.stackexchange.com/questions/6810/automatically-adding-doi-fields-to-a-hand-made-bibliography
#see also https://github.com/torfbolt/DOI-finder
#which uses http://www.crossref.org/guestquery (Form2)
def searchdoi(title='a model of  leptons', author='Weinberg'):
  """
  Search for the DOI given a title; e.g.  "A model of  leptons" (case insensitive), 
                     and the Surname (only) for the first author, e.g. Weinberg
  """
  params = urllib.urlencode({"titlesearch":"titlesearch", "auth2" : author, "atitle2" : title, "multi_hit" : "on", "article_title_search" : "Search", "queryType" : "author-title"})
  headers = {"User-Agent": "Mozilla/5.0" , "Accept": "text/html", "Content-Type" : "application/x-www-form-urlencoded", "Host" : "www.crossref.org"}
  conn = httplib.HTTPConnection("www.crossref.org:80")
  conn.request("POST", "/guestquery/", params, headers)
  response = conn.getresponse()
  # print response.status, response.reason
  data = response.read()
  conn.close()
  result = re.findall(r"\<table cellspacing=1 cellpadding=1 width=600 border=0\>.*?\<\/table\>" ,data, re.DOTALL)
  if (len(result) > 0):
    doitmp=urllib.unquote_plus(result[0])
    #print doitmp
    doi=re.sub('.*dx.doi.org\/(.*)<\/a>.*','\\1',doitmp)
    if re.search('No DOI found',doi):
       doi=''
  else:
    doi=''
    
  return doi

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
       * Colciencias Publindex Clasification or in general quartile information
       * Authors from the profile as defined in fullnames.py dictionary 
               or the others forms of the name as defined in the 
               author alias dictionary at authors.py
       * Groups at which the authors belong as defined in dictionary at 
               groups.py  
    TODO:
      * DOI

    Output cvs file under cvsfile below. 
    """
    #===OUTPUT FORMAT====
    IF_UdeA=True
    #====================
    debug=False;disable_publindex=False
    update=False #TODO: Implement as command line
    if debug:
        #WARNING: Just to have the program to run faster in debug mode
        disable_publindex=True
        publindex=[]    
        
    csvfile='newcitations'
    if update: 
        fl = open('%slog.py' %csvfile,'a')

    fj=open('issn.py','a')
    #Initialize output (empty) pandas DataFrame
   
    try:
        g=pd.read_csv('citations.csv')
        #remove phantom character from first key of the Google-Scholar profile output file
        g.columns=['Authors']+list(g.columns[1:])
        #intialize empty columns
        g['ISSN']=''
        g['Colciencias Clasification']='' #Or journal quartile in general
        g['DOI']='';g['Impact Factor']=''
        if IF_UdeA:
            g['Type']='';g['Group']='';g['Institution Authors']=''
            
        if disable_publindex or not IF_UdeA:
            print 'WARNING: publindex Data Frame not loaded. Check disable_publindex'
        else:
            print('Loading publindex data base ...')
            publindex=read_google_cvs(gss_key='0AjqGPI5Q_Ez6dHV5YWY4MEdFNUs0eW1aeEpoNWJKdEE',gss_query="select *")
            print 'Publindex loaded:',publindex.columns
        for i in range(g.shape[0]):
            #Convert NaN float to empty string
            if g['Publication'][i] != g['Publication'][i]:
                g['Publication'][i]=''

            if update:
                logkey=g['Publication'][i].replace(' ','')+'.'+str(g['Volume'][i])+'.'+str(g['Pages'][i])
            else:
                logkey='NoUpdate'
            
            #check if item already exists
            if not entry.has_key(logkey):
                if journal_alias.has_key(g['Publication'][i]):
                  #replace specific cell inside a pandas DataFrame  
                  g['Publication'][i]=journal_alias[g['Publication'][i]]

                #journal defintion necessary for proper treatment of empty and arXiv entries
                journal=g['Publication'][i]
                if not journal:
                    journal=''
                    issn[journal]=['0000-0000','00']
                    
                if journal.upper().find('ARXIV')>=0:
                    journal='Arxiv'
                    issn[journal]=['0000-0000','00']                
                    
                #general function to obtain: TODO: category_value (no yet necessary)
                #doi,issn_value,category_value=in_general()          
                #NOT UPDATE issn DICTIONARY!
                
                #Update ISSN column: 
                #if IF_UdeA: 
                issn_value,category_value,auth_group,auth_institute,typepub=in_physcs_udea(g.ix[i],issn,debug)
                if not issn.has_key(journal):            
                    issn[journal]=[issn_value,category_value]
                    fj.write("issn['%s']=['%s','%s']\n" %(journal,issn_value,category_value))
                    

                #g['DOI']=
                g['ISSN'][i]=issn[journal][0]
                g['Colciencias Clasification'][i]=issn[journal][1]
                if IF_UdeA:
                    g['Type'][i]=typepub
                    g['Group'][i]=auth_group; g['Institution Authors'][i]=auth_institute

                if update:
                    fl.write(r"entry['%s']=True" %logkey)
                    fl.write('\n')


    finally:
        if IF_UdeA:
            df=out_physics_udea(g)
        else:
            df=g
            
        df.to_csv('%s.csv' %csvfile,index=False)
        fj.close()
        if update:
            fl.close()

