#!/usr/bin/env python
from pybtex.database.input import bibtex
tipo='internacional'
issn='-'
parser = bibtex.Parser()
bib_data = parser.parse_file('diego.bib')
#bib_data.entries['Choi:2010jt'].persons['author'][0].last()[0]
#bib_data.entries['Choi:2010jt'].fields['doi']
for i in bib_data.entries.keys():
    n=len(bib_data.entries[i].persons['author'])
    authors=''
    for j in range(n):
        last=bib_data.entries[i].persons['author'][j].last()[0]
        if last!='others':
            first=bib_data.entries[i].persons['author'][j].bibtex_first()[0]

        if last=='Restrepo':
            last=last+' Quintero'
        if last=='others':
            first='Diego Alejandro'
            last='Restrepo Quintero; et al'

        authors=authors+first+' '+last+'; '#+\

    title=bib_data.entries[i].fields['title'].replace('{','').replace('}','')
    cvsentry=bib_data.entries[i].fields['year']+' , '\
              +tipo+' , '+authors+' , '\
              +bib_data.entries[i].fields['journal']+' , '\
              +bib_data.entries[i].fields['volume']+' , '\
              +bib_data.entries[i].fields['pages']+' , '\
              +issn+' , '\
              +title

    print cvsentry

