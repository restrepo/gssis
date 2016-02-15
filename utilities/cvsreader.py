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
import os.path
import commands
#python database dictionaries
from issn import *
from newcitationslog import * #defines dictionay entry
from journal_alias import *
import pickle #http://stackoverflow.com/questions/11218477/how-to-use-pickle-to-save-a-dict
#Plugins
from InsitutoFisicaUdea import *

#TODO; move al tha databases to pickle
#  SUPER_TODO: move to sqlite: See discussion at:
#  http://stackoverflow.com/questions/14029077/pickle-to-file-instead-of-using-database
#with open('impactfactors.pickle', 'rb') as handle:
impact_factors= pd.read_pickle('impactfactors.pickle')

#functions

def clean_dictionaries(dictionary=impact_factors,filename='impactfactors.pickle'):
    import pickle
    dictionary={}
    #with open('impactfactors.pickle', 'wb') as handle:
    #    pickle.dump(dictionary, handle)
    pd.to_pickle(dictionary,file)
    

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
    
#UDPATE in repo
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
        dataset = [eval(td.get_text().replace('-','0')) for td in row.find_all("td") if td.get_text().strip()]
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
lower_first_char = lambda s: s[:1].lower() + s[1:] if s else ''
def search_doi(surname='Florez',\
    title=r'Baryonic violation of R-parity from anomalous $U(1)_H$',other=''):
    '''
    Search doi from http://search.crossref.org/ 
    '''
    import re
    import requests
    doi={}
    search=''
    if surname:
        search=surname
    if title:
        if len(search)>0:
            search=search+', '+title
    if other:
        if len(search)>0:
            search=search+', '+other
            
    r=requests.get('http://search.crossref.org/?q=%s' %search)
    urldoi='http://dx.doi.org/'
    doitmp=''
    if len(r.text.split(urldoi))>1:
       doitmp=r.text.split(urldoi)[1].split("\'>")[0].replace('&lt;','<').replace('&gt;','>')
    #check doi is right by searching for all words in doi -> output title
    if doitmp:
        json='https://api.crossref.org/v1/works/'
        rr=requests.get( json+urldoi+doitmp )
        if rr.status_code==200:
           if rr.json().has_key('message'):
              chktitle = re.sub(r"\$.*?\$","",title) # better remove all math expressions
              chktitle = re.sub(r"[^a-zA-Z0-9 ]", " ", chktitle).split(' ')
              if chktitle:
                 if not -1 in [(rr.json()["message"]['title'][0]).find(w)  for w in chktitle]:
                    doi=rr.json()["message"]
                    
    return doi

def searchdoi(title='a model of  leptons', surname='Weinberg'):
    """
    Search for the metadata of given a title; e.g.  "A model of  leptons" 
   (case insensitive), and the Surname (only) for the first author, 
    e.g. Weinberg 
                      
    returns a dictionary with the keys:

       ['Article Title','Author','ISSN','Volume','Persistent Link','Year',
        'Issue','Page','Journal Title'],

       where 'Author' is really the surname of the first author
    """
    import mechanize
    import re
    from bs4 import BeautifulSoup
    
    browser = mechanize.Browser()
    browser.set_handle_robots(False)
    browser.addheaders = [('User-agent', 'Firefox')] 
    browser.open("http://www.crossref.org/guestquery/")
    assert browser.viewing_html()
    browser.select_form(name="form2")
    # use only surname of first author
    browser["auth2"] =  surname
    browser["atitle2"] = title
    response = browser.submit()
    sourcecode = response.get_data()
    result = re.findall(r"\<table cellspacing=1 cellpadding=1 width=600 border=0\>.*?\<\/table\>" ,sourcecode, re.DOTALL)
    if len(result) > 0:
        html=result[0] 
        #doi=re.sub('.*dx.doi.org\/(.*)<\/a>.*','\\1',doitmp)
        if re.search('No DOI found',html):
            html='<table><tr><td>No DOI found<td></tr></table>'
    else:
        html=''

    soup = BeautifulSoup(html)
    table = soup.find("table")

    dataset = []
    for row in table.find_all("tr"):
        for tdi in row.find_all("td"):
            dataset.append(tdi.get_text())
            
    if len(dataset)==20:
        headings=dataset[:9]
        datasets=dataset[10:-1]
        doi=dict(zip(headings,datasets))
        print '=> ',doi['Persistent Link'].replace('http://dx.doi.org/','')
        
    else:
        doi=search_doi(surname,title)
        if doi.has_key('DOI'):
           print '... ',doi['DOI']
           doi['Persistent Link']=doi['DOI']
        if doi.has_key('ISSN'):
           doi['ISSN']=doi['ISSN'][0]
        
    if doi:
        if doi.has_key('ISSN') and doi.has_key('Persistent Link'):
            doi['ISSN']=re.sub('([a-zA-Z0-9]{4})([a-zA-Z0-9]{4})','\\1-\\2',doi['ISSN'])
            doi['Persistent Link']=doi['Persistent Link'].replace('http://dx.doi.org/','')
            
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
    #DEBUG
    doi={}
    #===OUTPUT FORMAT====
    IF_UdeA=True
    #====================
    debug=False;disable_publindex=False;publindex_pandas=True
    update=True #TODO: Implement as command line
    if debug:
        #WARNING: Just to have the program to run faster in debug mode
        disable_publindex=True
        publindex=[]    
        
    #clean cache TODO: for all dictionaries
    Clean_Cache=False
    if Clean_Cache:
        impact_factors={}
    
    csvfile='newcitations'
    csvfilelog='%slog.py' %csvfile
   
    if not os.path.isfile(csvfilelog) or os.path.getsize(csvfilelog) == 0:
        fl = open(csvfilelog,'w')
        fl.write("#!/usr/bin/env python\n# -*- coding: utf-8 -*-\n#Don't delete the next line!\nentry={}\n")
        fl.close()
    
    
    fl = open(csvfilelog,'a')
    fj=open('issn.py','a')
    
    #Initialize output (empty) pandas DataFrame
  
    if 1==1:
        #prepare citations.csv
        #DEBUG: check missing number
        #tmp=commands.getoutput("cat citations.csv | grep -v ',,,' |  grep -Ev '[A-Za-z\)],,[0-9]*[a-zA-Z],[0-9]*' |  grep -Ev '[A-Za-z\)],[0-9]*,,[0-9]*' > kk && mv kk citations.csv")
        tmp=commands.getoutput("cat citations.csv | grep -v ',,,' |  grep -Ev '[A-Za-z\)],,[0-9]*[a-zA-Z],[0-9]*' > kk && mv kk citations.csv")
        g=pd.read_csv('citations.csv')
        ##remove phantom character from first key of the Google-Scholar profile output file
        g.columns=['Authors']+list(g.columns[1:])
        g=g[~g.Publication.str.lower().str.contains('arxiv')].reset_index(True)
        #remove nan
        g=g.fillna(value='')
        #intialize empty columns
        g['New_entry']=False
        g['ISSN']=''
        g['NEWISSN']=''
        g['Colciencias Clasification']='' #Or journal quartile in general
        g['DOI']='';g['Impact Factor']=''
        if IF_UdeA:
            g['Type']='';g['Group']='';g['Institution Authors']=''
            
        if disable_publindex or not IF_UdeA:
            print 'WARNING: publindex Data Frame not loaded. Check disable_publindex'
        else:
            print('Loading publindex data base ...')
            if publindex_pandas and os.path.isfile('publindex.csv'):
		print('From pandas DataFrame. Check publindex_pandas')
                publindex=pd.read_csv('publindex.csv')
            else:
            	publindex=read_google_cvs(gss_key='0AjqGPI5Q_Ez6dHV5YWY4MEdFNUs0eW1aeEpoNWJKdEE',gss_query="select *")
                publindex.to_csv('publindex.csv',index=False)

            print 'Publindex loaded:',publindex.columns
            
        #Dictionary with keys: issn, and values: panda Data Frames with IF info
        #impact_factors={} -> pickle managed
        for i in range(g.shape[0]):
            #Convert NaN float to empty string
            if g['Publication'][i] != g['Publication'][i]:
                g['Publication'][i]=''

            #remove all non-standard characters
            logkey=(re.sub(r"[^a-zA-Z0-9 ]","",str(g['Publication'][i])).replace(' ','')+'.'+str(g['Volume'][i])+'.'+str(g['Pages'][i]))
            logkeybak=logkey
            if not update:
                logkey='NoUpdate'
            
            #check if item already exists
            if not entry.has_key(logkey):

                g['New_entry'][i]=True
                if not update: #recover logkey
                    logkey=logkeybak
            
                #general function to obtain: 
                #  doi,issn_value,category_value=in_general()
                #  TODO: category_value (no yet necessary)
                #NOT UPDATE issn DICTIONARY!
                
                if not entry.has_key(logkey):
                    #WARNING: Only Surname of first author
                    surname=g['Authors'][i].split(';')[0].split(',')[-2].strip()
                    #If several surnames pick the last one
                    surname=re.sub('.*\s(.*)','\\1',surname) 
                    doi=searchdoi(g['Title'][i],surname)
                    if doi.has_key('Persistent Link'):
                        g['NEWISSN'][i]= doi['ISSN']
                        g['DOI'][i] = doi['Persistent Link']
                        doi={} #reset values
                    #journal_alias DB is still manually generated
                    #TODO: Obtain official journal name
                    #      from -> def searchdoi(title,surname)
                    #      and update journal_alias databases
                else:
                    g['NEWISSN'][i]= entry[logkey][0]
                    g['DOI'][i] = entry[logkey][1]
                
                if journal_alias.has_key(g['Publication'][i]):
                    #replace specific cell inside a pandas DataFrame
                    #TODO: From manual to automatic journal_alias
                    g['Publication'][i]=journal_alias[g['Publication'][i]]
                    
                if g['Publication'][i]==0:
                    g['Publication'][i]=''

                #Update data frame ISSN column: 
                if IF_UdeA: 
                    #journal defintion necessary for proper treatment of empty and arXiv entries
                    #TODO: move this code to function even in v<2. Copy journal definition after
                    #   function call
                    journal=str(g['Publication'][i])
                    if not journal:
                        journal=''
                        issn[journal]=['0000-0000','00']
                    
                    if journal.upper().find('ARXIV')>=0:
                        journal='Arxiv'
                        issn[journal]=['0000-0000','00']                
                        
                    issn_value,category_value,auth_group,auth_institute,typepub=in_physcs_udea(g.ix[i].to_dict(),issn,publindex)
                    if not issn.has_key(journal):            
                        issn[journal]=[issn_value,category_value]
                        fj.write("issn['%s']=['%s','%s']\n" %(journal,issn_value,category_value))
                    


                    g['NEWISSN'][i]=issn[journal][0]
                    g['ISSN'][i]=g['NEWISSN'][i] #issn[journal][0]
                    g['Colciencias Clasification'][i]=issn[journal][1]
                    g['Type'][i]=typepub
                    g['Group'][i]=auth_group; g['Institution Authors'][i]=auth_institute
                else:
                    g['ISSN'][i]=g['NEWISSN'][i]
                #Impact factor
                #Convierta Anyo a entero
                if g['Year'][i]=='':
                    g['Year'][i]=0 #'null'
                #if g['Year'][i]!='null':
                g['Year'][i]=int(g['Year'][i])
                    
                if not impact_factors.has_key(g['ISSN'][i]):
                    impact_factors[g['ISSN'][i]]=getIF(g['ISSN'][i])
 
            
                IF=impact_factors[g['ISSN'][i]]
                #If (Published_year-1) in range of Impact_Factor Years, set IF, else
                #If (Published_year-1) in range  too old (too new) -> set to older IF (newer IF)
                if len(IF)>0 and g['Year'][i]!='null':
                    if g['Year'][i]-1 < IF['Year'][4]:
                        g['Impact Factor'][i]=IF['Impact Factor (IF)'][4]
                    elif g['Year'][i]-1 > IF['Year'][0]:
                        g['Impact Factor'][i]=IF['Impact Factor (IF)'][0]
                    else:
                        g['Impact Factor'][i]=IF[IF['Year']==(g['Year'][i]-1)]['Impact Factor (IF)'].values[0]


                if not entry.has_key(logkey):
                    if not g['DOI'][i]:
                        g['DOI'][i]='Not DOI'
                    if not g['ISSN'][i]:
                        g['ISSN'][i]='0000-0000'
                    
                    #WARNING: the last obtained ISSN will be used
                    fl.write(r"entry['%s']=['%s','%s']" %(logkey,g['ISSN'][i],g['DOI'][i]))
                else:
                    if not entry.has_key(logkey):
                        fl.write(r"entry['%s']=['','']" %logkey)
                        
                if not entry.has_key(logkey):
                        fl.write('\n')


#    finally:
        if update:
           g=g[g['New_entry']]
        if IF_UdeA:
           df=out_physics_udea(g)
        else:
           df=g[['Year','Authors','Publication','Volume','Pages','ISSN'\
                'Title','DOI','Type II','Colciencias Clasification','Impact Factor']]
        #save pandas data frame: http://goo.gl/eZm6pi
        g.to_pickle('newcitations.df')
        #load as 
        #g=pd.load('newcitations.df')
        if df.shape[0]>0:
           df.to_csv('%s.csv' %csvfile,index=False)
        else:
           print 'No new references to update'
        fj.close()
        fl.close()
        #Save dictionaries with pickle
        #with open('impactfactors.pickle', 'wb') as handle:
        #    pickle.dump(impact_factors, handle)
        pd.to_pickle(impact_factors,'impactfactors.pickle')

            


