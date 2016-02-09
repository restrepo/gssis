#!/usr/bin/env python
# -*- coding: utf-8 -*-
#python database dictionaries
##
import pandas as pd
import numpy as np
import urllib
#It recommended to use http://python-requests.org instead
from pygoogle import pygoogle #From: https://code.google.com/p/pygoogle/
from bs4 import BeautifulSoup
import httplib
import re #, sys, cgi
import sys
##
from authors import *
from groups import *
from national import *
from fullnames import *
def in_physcs_udea(input_dict,issn_dict,publindex):
    """
     Input
    """
    journal=str(input_dict['Publication'])

    if journal.upper().find('ARXIV')>=0:
        journal='Arxiv'
        #already have issn='0000-0000'
        
    if issn_dict.has_key(journal):
        issn_value=issn_dict[journal][0]
        category_value=issn_dict[journal][1]
    else:
        jf=pd.DataFrame()
        if len(publindex[publindex['NOMBRE'].str.contains(journal.upper())])>0:
            jf=publindex[publindex['NOMBRE'].str.contains(journal.upper())].sort(['CALIFICACION'],ascending=True).reset_index(drop=True)

        if jf.shape[0]>0:
            issn_value=jf['ISSN'][0]
            category_value=jf['CALIFICACION'][0]
        else:    
            issn_value='0000-0000'
            category_value='00'
    
    auth_group,auth_institute=get_authors_info(input_dict,authors,groups,fullnames)
    #nal o inal
    typepub='Internacional'
    if national.has_key(input_dict['Publication']): 
        typepub='Nacional'
                
    return issn_value,category_value,auth_group,auth_institute,typepub

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

def meval1(s):
    if s:
        return -eval(s)+1 # -1 when len(str.split('-'))<2
    else:
        return s
    
def out_physics_udea(df):
    '''
    Data Frame Output for Insituto de Fisica - Universidad de Antioquia
    '''
    df['Impreso']='';df['PDF']='';df['Proyect ID']=''
    df['Type II']=''
    df['Proyecto']=''
    df['Pages']=(df['Pages'].astype(str)).str.replace(r'_','')
    df['Pages']=df['Pages'].str.replace(r'[a-zA-Z \.]','')
    df['NPages']=df['Pages'] 
    df['Pages']=df['Pages'].str.replace(r'-[0-9]+','')
    df['NPages']=df['NPages'].str.replace(r'^[0-9]+$','2')
    df['NPages']=(df['NPages'].str.replace(r'^0','')).str.replace(r'-0','')
    df['Year']         = df['Year'].astype(int)
    df['Volume']       = df['Volume'].replace('',0)
    df['Volume']       = df['Volume'].astype(int)
    df['Impact Factor']= df['Impact Factor'].astype(str).str.replace('.',',')


    df['NPages']=df['NPages'].apply(meval1)
    return df[['Year','Type','Authors','Publication','Volume','Pages','ISSN',\
       'Title','Impreso','PDF','Group','DOI','Type II','Proyect ID',\
               'Institution Authors','Colciencias Clasification','Impact Factor',\
               'Proyecto','NPages']]
