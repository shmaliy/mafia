// JavaScript Document
function add_to_basket(id){
	var par = {
		'function' : 'dynamic->add_to_basket',
		'id' : id
	};
	new Ajax.Request('/index.php', {
		method: 'post',
		parameters: par,
		onCreate : (function(){}).bind(this),
		onComplete : (function(request){
			var resp = request.responseText.evalJSON();
			$("fp_basket_text").update(resp['basket_fp']);
			$("basket").update(resp['basket']);
		}).bind(this)
	});		
}
function clear_basket(){
	var par = {
		'function' : 'dynamic->clear_basket'
	};
	new Ajax.Request('/index.php', {
		method: 'post',
		parameters: par,
		onCreate : (function(){}).bind(this),
		onComplete : (function(request){
			var resp = request.responseText.evalJSON();
			$("fp_basket_text").update(resp['basket_fp']);
			$("basket").update(resp['basket']);
		}).bind(this)
	});		
}
function remove_from_basket(id){
	var par = {
		'function' : 'dynamic->remove_from_basket',
		'id' : id
	};
	new Ajax.Request('/index.php', {
		method: 'post',
		parameters: par,
		onCreate : (function(){}).bind(this),
		onComplete : (function(request){
			var resp = request.responseText.evalJSON();
			$("fp_basket_text").update(resp['basket_fp']);
			$("basket").update(resp['basket']);
		}).bind(this)
	});		
}

function page(i){
	var par = {
		'function' : 'set_page',
		'page' : i,
		'location' : window.location.href
	};
	new Ajax.Request('/index.php', {
		method: 'post',
		parameters: par,
		onCreate : (function(){}).bind(this),
		onComplete : (function(request){
			window.location.href = par['location'];
		}).bind(this)
	});		
}
