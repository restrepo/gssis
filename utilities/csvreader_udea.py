#!/usr/bin/env python
# -*- coding: utf-8 -*-
from csvreader import *
#entry={}
def savecsv(c,csvfile,update,out_type='udea',partial=False):
    df=pd.DataFrame()
    if update:
        c=c[c['New_entry'].astype(bool)]
    if out_type=='udea':
        df=out_physics_udea(c)
    if df.shape[0]>0:
        df.to_csv('%s.csv' %csvfile,index=False)
    else:
        if not partial:
            print 'No new references to update'

if __name__=='__main__':
    """
    DEBUG: Year broken, RCF as Internacional
    Options to run:
       --no-update: generate file from schratch
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

    update=True
    if len(sys.argv)>1:
        if sys.argv[1]=='--no-update':
            update=False
    debug=False;disable_publindex=False;publindex_pandas=True
     #TODO: Implement as command line
    if not update:
        entry={}
    
    csvfile='newcitations'
    csvfilelog='%slog.py' %csvfile
   
    if not os.path.isfile(csvfilelog) or os.path.getsize(csvfilelog) == 0:
        fl = open(csvfilelog,'w')
        fl.write("#!/usr/bin/env python\n# -*- coding: utf-8 -*-\n#Don't delete the next line!\nentry={}\n")
        fl.close()
    
    
    fl = open(csvfilelog,'a')
    fj = open('issn.py','a')
    tmp=commands.getoutput("cat citations.csv | grep -v ',,,' |  grep -Ev '[A-Za-z\)],,[0-9]*[a-zA-Z],[0-9]*' > kk && mv kk citations.csv")
    gg=pd.read_csv('citations.csv')
    ##remove phantom character from first key of the Google-Scholar profile output file
    gg.columns=['Authors']+list(gg.columns[1:])
    gg=gg[~gg.Publication.str.lower().str.contains('arxiv')].reset_index(True)
    #remove nan
    gg=gg.fillna(value='')
    
    if disable_publindex:
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
            
    
    c=pd.DataFrame()
    
    
    for i in range(gg.shape[0]):
        if i%100==0:
            print i
        g=pd.Series(gg.ix[i])
        g['DOI']='';g['ISSN']=''
        if g.Publication != g.Publication:
                g.Publication=''

        #remove all non-standard characters
        logkey=(re.sub(r"[^a-zA-Z0-9 ]","",str(g.Publication)).replace(' ','')+'.'+str(g.Volume)+'.'+str(g.Pages))
            
        #check if item already exists
        if entry.has_key(logkey):
            g['New_entry']=False
        else:
            g['New_entry']=True
            
        #WARNING: Only Surname of first author
        if not entry.has_key(logkey):
            #special cases:
            if g.Authors=='CMS collaboration':
                g.Authors='CMS, collaboration'

            if len(g.Authors.split(';')[0].split(','))>1:
                surname=g.Authors.split(';')[0].split(',')[-2].strip()
                #If several surnames pick the last one
                surname=re.sub('.*\s(.*)','\\1',surname)
            else:
                surname=''
                
            doi=searchdoi(g.Title,surname)
            if doi.has_key('Persistent Link'):
                g['DOI'] = doi['Persistent Link']
            if doi.has_key('ISSN'):
                g['ISSN']= doi['ISSN']

        
               #journal_alias DB is still manually generated
               #TODO: Obtain official journal name
               #   from -> def searchdoi(title,surname)
               #      and update journal_alias databases        
        else:
            g['ISSN']= entry[logkey][0]
            g['DOI'] = entry[logkey][1]  
            
        if journal_alias.has_key(g.Publication):
            g.Publication=journal_alias[g.Publication]

        journal=str(g.Publication)
        if not journal:
            journal=''
                    
        if journal.upper().find('ARXIV')>=0:
            journal='Arxiv'
            issn[journal]=['0000-0000','00']    
                        
        issn_value,category_value,auth_group,auth_institute,typepub=in_physcs_udea(g,issn,publindex)
        if not issn.has_key(journal):            
            issn[journal]=[issn_value,category_value]
            fj.write("issn['%s']=['%s','%s']\n" %(journal,issn_value,category_value))

        #overwrite ISSN
        if issn[journal][0]:
            g['ISSN']=issn[journal][0]
            
        g['Colciencias Clasification']=issn[journal][1]
        g['Type']=typepub
        g['Group']=auth_group; g['Institution Authors']=auth_institute

        #Impact factor
        #Convierta Anyo a entero
        if g.Year=='' or g.Year == 'null':
            g.Year=0 #'null'
            g.Year=int(g.Year)

                    
        if not impact_factors.has_key(g.ISSN):
            impact_factors[g.ISSN]=getIF(g.ISSN)
 
            
        IF=impact_factors[g.ISSN]
        #If (Published_year-1) in range of Impact_Factor Years, set IF, else
        #If (Published_year-1) in range  too old (too new) -> set to older IF (newer IF)
        if len(IF)>0:
            if not 'Impact Factor (IF)' in IF:
                if 'IF' in IF:
                    IF['Impact Factor (IF)']=IF['IF']
                else:
                    IF['Impact Factor (IF)']=-1
            if g.Year-1 < IF['Year'][4]:
                g['Impact Factor']=IF['Impact Factor (IF)'][4]
            elif g.Year-1 > IF['Year'][0]:
                g['Impact Factor']=IF['Impact Factor (IF)'][0]
            else:
                g['Impact Factor']=IF[IF['Year']==(g.Year-1)]['Impact Factor (IF)'].values[0]
        else:
            g['Impact Factor']=-1


        if not entry.has_key(logkey):
            if not g['DOI']:
                g['DOI']='Not DOI'
            if not g['DOI']:
                g['ISSN']='0000-0000'
                    
            #WARNING: the last obtained ISSN will be used
            if i>1000:
                print i,logkey,g.ISSN,g.DOI
            fl.write(r"entry['%s']=['%s','%s']" %(logkey,g.ISSN,g.DOI))
            fl.write('\n')
            
        #To the end
        c=c.append(g,ignore_index=True)
        savecsv(c,csvfile+'_partial',update,out_type='udea',partial=True)

    fl.close();fj.close()        
    #Save dictionaries with pickle
    savecsv(c,csvfile,update,out_type='udea')
    #with open('impactfactors.pickle', 'wb') as handle:
    #    pickle.dump(impact_factors, handle)
    pd.to_pickle(impact_factors,'impactfactors.pickle')
