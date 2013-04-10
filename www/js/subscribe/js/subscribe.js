var Subscribe = Class.create();

Subscribe.prototype = {
	intv: null,
	initialize: function(element, options)
    {
    	this.element = $(element) || false;
    	this.options = options || {};
    	this.options.onSubscribe = options.onSubscribe || function(){};
    	this.options.encoding = options.encoding || 'utf-8';
    	
    	/* build html */
    	this.form = new Element('form', {'id': 'widjet-subscribe-form', 'action': '#'});
    	this.form.insert(new Element('label', {'id': 'widjet-subscribe-label'}).update(this.options.labelText || 'E-mail'));
    	this.form.insert(new Element('input', {'id': 'widjet-subscribe-input', 'type': 'text', 'name':'email'}));
    	this.form.insert(new Element('input', {'id': 'widjet-subscribe-submit', 'type': 'submit', 'value': this.options.buttonText || 'Subscribe'}));
    	this.form.insert(this.status = new Element('div', {'id': 'widjet-subscribe-status'}));
    	this.element.update(this.form);
    	
    	/* observe events */
    	this.form.observe('submit', (function(event){ event.stop(); this.submit(); }).bindAsEventListener(this));
    },
    submit: function()
    {
    	/* clear progress bar */
    	window.clearInterval(this.intv);
    	
    	/* check empty field */
    	if (this.form.email.value == '') {
    		this.status.update(this.options.ERROR_EMPTY || 'fill email field');
    		return;
    	}
    	
    	/* send request to server */
    	new Ajax.Request('/js/subscribe/php/index.php', {
    		method: 'post',
    		parameters: {'function': 'Subscribe->submit', 'email': this.form.email.value, 'encoding': this.options.encoding},
    		onCreate : (function(){
    			/* initialize progress bar */
    			this.intv = setInterval((function(){
					if (this.status.innerHTML.length < 10){
						this.status.insert('&bull;');					
					} else {
						this.status.update('');
					}
				}).bind(this), 100);
    		}).bind(this),
    		on404: (function(){
    			window.clearInterval(this.intv);
    			this.status.update('404');
    		}).bind(this),
    		onComplete : (function(response){
    			window.clearInterval(this.intv);
    			eval(response.responseText);
    			this.options.onSubscribe();
    		}).bind(this)
    	});		
    }
};
