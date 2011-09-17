
var SS_URL = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE";

$.ss(SS_URL)
  .setQuery("select * where B contains ''")
  .setField("C,D,H,E,F,G,K,L")
  .send(function(success){
    if(!success) return;
    var con = $('#content')
    var str = "<table class='templateTable'>"
    this.each(function(i, k){
      str += "<tr><td>" + this['H'] + "</td></tr>"
     })
    str += "</table>"
    con.html(con.html() + str)
  });
