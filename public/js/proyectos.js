
var SS_URL = "http://spreadsheets.google.com/tq?key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE";

$.ss(SS_URL)
  .setQuery("select C,D,H,E,F,G,K,L where B contains ''")
  .setField("C,D,H,E,F,G,K,L")
  .send(function(success){
    if(!success) return;
    var con = $('#content')
    var str = "<table class='templateTable'>"
    this.each(function(i, k) {
      str += "<tr><td colspan='4'><b>Proyecto " + (i+1) + ": " + this['C'] + "</b></td></tr>\
              <tr><td colspan='4'>Investigador principal: " + this['D'] + "</td></tr>\
              <tr><td>Financiadores</td><td colspan='3'>" + this['H'] + "</td></tr>\
              <tr><td>Convocatoria</td><td colspan='3'>" + this['E'] + "</td></tr>\
              <tr><td>Fecha de inicio</td><td>" + this['F'] + "</td><td>Duración en meses<br>o fecha de<br>finalización</td><td>" + this['G'] + "</td></tr>\
              <tr><td>Valor total en $ millones</td><td>" + this['K'] + "</td><td>% Cofinanciación U.<br>de A.</td><td>" + this['L'] + "</td></tr>"
     })
    str += "</table>"
    con.html(con.html() + str)
  });
