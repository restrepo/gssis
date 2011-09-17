;(function($){

	var RANDOM_BASE = "abcdefghijklmnopqrstuvwxyz0123456789";
	var CALLBACK_PREFIX = "sscallback";
	var DEFAULT_PARAM = {
		version : "0.6"
	};
	var reqId = 0;
	function getRandom(length) {
		length = length || 10;
		var r = "";
		while(r.length<length) {
			r += RANDOM_BASE.charAt(Math.floor(RANDOM_BASE.length*Math.random()))||"";
		}
		return r;
	}
	function serializeParam(param) {
		var r = [];
		param.out = "json";
		for(var i in param) {
			r.push([i,param[i]].join(":"));
		}
		return r.join(";");
	}
	function ss(url,param) {
		param = $.extend(DEFAULT_PARAM,param);
		var _query;
		var _field;
		function setQuery(query) {
			_query = query;
			return rtn;
		}
		function setField(field) {
			_field = field;
			return rtn;
		}
		function complete(res,callback) {
			if(/^(ok|warning)$/.test(res.status)) {
				var tbl = res.table;
				var rows = [];
				var fld = _field || [];
				if(typeof fld == "string") fld = fld.split(",")
				$.each(tbl.rows,function(){
					var obj = {};
					var row = this;
					var cols = row.c;
					$.each(cols,function(i){
						obj[fld[i]||i] = (this||{}).v;
					});
					rows.push(obj);
				});
				callback.apply($(rows),[true]);
			} else {
				callback.apply({
					errors : res.errors||[]
				},[false]);
			}
			
		}
		function send(callback) {
			var tqx = "";
			var rid = reqId++;
			do {
				var handlerName = CALLBACK_PREFIX+getRandom(20);
			} while(window[handlerName]);
			window[handlerName] = function(res){
				if(res.reqId==rid.toString()) complete(res,callback);
				setTimeout(function(){
					try {
						delete window[handlerName];
					} catch(e) {};
				},1);
			};
			param.responseHandler = handlerName;
			param.reqId = rid;
			$.getScript(url+"&tqx="+serializeParam(param)+"&tq="+encodeURIComponent(_query||""));
		}
		var rtn = {
			setQuery : setQuery,
			setField : setField,
			send : send
		}
		return rtn;
	}
	$.extend({"ss":ss});
})(jQuery);