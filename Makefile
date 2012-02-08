clean:
	find . -name *~ -exec rm '{}' \;
indent:
	find . -name *.php -exec  astyle '{}' \;	
