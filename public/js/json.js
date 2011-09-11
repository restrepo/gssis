
var Type = {"Cien" : 0, "Colcien" : 1};
var Format = {"txt" : 0, "html" : 1 };

function getJsonAjaxRequest(url)
{
  var ajaxRequest = new ajaxObject(url);
      return ajaxRequest;
}

function processArticle(ajaxRequest,type,format)
{
  var info;
  switch(type)
  {
    case Type.Cien :
      //get info for cien format
      break;
    case Type.ViceInves :
      //get information sorted for 
      break;
  }
}

function processProject(ajaxRequest,format)
{
  
}

