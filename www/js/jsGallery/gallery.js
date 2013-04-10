var jsGallery = Class.create();

jsGallery.prototype = {
    aImages: [],
	iActive: null,    
    initialize: function(container, options)
	{    
		/* Определение контейнера для лайтбокса */
		this.container = $(container);
		if (!this.container) {
			throw('Container is not defined');
		}
		
		/* Определение базовых настроек */
		this.conf = {};
		this.conf.animate   = options.animate   || true;
		this.conf.downscale = options.downscale || true;
		this.conf.numTpl    = options.numTpl    || 'Image %i of %t';
		this.conf.selector  = options.selector  || 'lightbox';
		this.conf.minSpace  = options.minSpace  || 50;
		this.conf.startSize = options.startSize || { width: 64, height: 64 };
		this.conf.ovOpacity = options.ovOpacity || 0.7;
		this.conf.captionMaxHeight = options.captionMaxHeight || 30;
		
		/* Определение функций обратного вызова */
		this.call = {};
		this.call.onInitialize = options.onInitialize || Prototype.emptyFunction;
		this.call.onStart      = options.onStart      || Prototype.emptyFunction;
		this.call.onChange     = options.onChange     || Prototype.emptyFunction;
		this.call.onEnd        = options.onEnd        || Prototype.emptyFunction;
		
		/* Определение времменых параметров */
		this.time = {};
		this.time.appear = options.timeAppear || 0.5;
		this.time.fade   = options.timeFade   || 0.5;
		this.time.resize = options.timeResize || 1000;
		
		this.updateArray();
		
		/* Создание разметки и добаление ее на страницу */
		this.eOverlay = new Element('div', {'class': 'lightbox-overlay'}).hide();
		this.eBody = new Element('div', {'class': 'lightbox-body'}).hide().insert(
			this.eImgContainer = new Element('div', {'class': 'lightbox-image-container'}).insert(
				this.eImg = new Element('img')
			).insert(
				this.eNav = new Element('div', {'class': 'lightbox-image-nav'}).hide().insert(
					this.eNavPrev = new Element('a', {'class': 'lightbox-image-prevLink', 'href': '#'})
				).insert(
					this.eNavNext = new Element('a', {'class': 'lightbox-image-nextLink', 'href': '#'})
				).insert(
					this.eNavClose = new Element('a', {'class': 'lightbox-image-closeLink', 'href': '#'})
				)
			).insert(
				this.eNavWait = new Element('div', {'class': 'lightbox-image-waitLink'}).update(
					new Element('div', {'class': 'lightbox-image-waitIcon'})
				)
			).insert(
				this.eImgData = new Element('div', {'class': 'lightbox-image-data'}).hide().insert(
					this.eImgCaption = new Element('div', {'class': 'lightbox-image-caption'}).hide()
				).insert(
					this.eImgNumber = new Element('div', {'class': 'lightbox-image-number'})
				)
			)
		);		
		this.container.insert(this.eOverlay).insert(this.eBody);
		
		/* Назначение действий */
		this.eOverlay.observe('click', this.end.bind(this));
		this.eNavClose.observe('click', this.end.bind(this));
		this.eNavWait.observe('click', this.end.bind(this));
		
		this.eNavPrev.observe('click', (function(event){
			event.stop();
			this.changeImage(this.iActive - 1);
		}).bindAsEventListener(this));
		
		this.eNavNext.observe('click', (function(event){
			event.stop();
			this.changeImage(this.iActive + 1);
		}).bindAsEventListener(this));
		
		this.call.onInitialize(this);
    },
	calcResizeParams: function()
	{
		var oldHeight = this.eImgContainer.getHeight();
		var oldWidth = this.eImgContainer.getWidth();
		var oldMarginTop = this.eImgContainer.getStyle('marginTop');
		
		var height = this.newHeight + this.conf.minSpace + this.conf.captionMaxHeight;
		var width = this.newWidth + this.conf.minSpace;
		
		if ((this.eOverlay.getDimensions().height <= height || this.eOverlay.getDimensions().width <= width) && this.conf.downscale) {
			if (this.eOverlay.getDimensions().height <= height && this.eOverlay.getDimensions().width > width) {
				height = this.eOverlay.getDimensions().height - (this.conf.minSpace * 2) - this.conf.captionMaxHeight;
				this.newWidth = (height / this.newHeight) * this.newWidth;
				this.newHeight = height;
			}
			if (this.eOverlay.getDimensions().height >= height && this.eOverlay.getDimensions().width <= width) {
				width = this.eOverlay.getDimensions().width - (this.conf.minSpace * 2);
				this.newHeight = (width / this.newWidth) * this.newHeight;
				this.newWidth = width;
			}
			this.eImg.height = this.newHeight;
			this.eImg.width = this.newWidth;
		}
		
		this.durResizeHeight = Math.abs(oldHeight - this.newHeight) / this.time.resize;
		this.durResizeWidth = Math.abs(oldWidth - this.newWidth) / this.time.resize;
		this.newMarginTop = (this.eOverlay.getDimensions().height - this.newHeight - this.conf.captionMaxHeight) / 2;
	},
    updateArray: function(selector)
	{   
		if (selector) { this.conf.selector = selector; }
		
        document.observe('click', (function(event){
            var target = event.findElement('a[rel^=' + this.conf.selector + ']');
            if (target) {
                event.stop();
                this.start(target);
            }
        }).bind(this));
    },
    start: function(eventElement)
	{
		this.call.onStart(this);
		
		this.aImages = [];
        this.iActive = 0;       

        if ((eventElement.rel == this.conf.selector)){
            /* Если картинка отдельная добавляем в массив */
            this.aImages.push({
				href:  eventElement.href,
				title: eventElement.title
			});         
        } else {
            /* Если картинка часть списка формируем список */
			var list = $$(eventElement.tagName + '[href][rel="' + eventElement.rel + '"]').uniq();
			list.each((function(el){
				var exists = false;
				for (var i = 0; i < this.aImages.length; i++) {
					if (this.aImages[i].href == el.href && this.aImages[i].title == el.title) {
						exists = true;
					}
				}
				
				if (!exists) {
					this.aImages.push({
						href:  el.href,
						title: el.title
					});
				}
			}).bind(this));
            
			while (this.aImages[this.iActive].href != eventElement.href) { this.iActive++; }
        }
        
		/* Прячем непослушные элементы */
		$$('select', 'object', 'embed').each(function(node){
			node.setStyle({visibility: 'hidden'});
		});
		
		/* Установка начальных размеров и позиции блока */
		this.newWidth  = this.conf.startSize.width;
		this.newHeight = this.conf.startSize.height;
		this.calcResizeParams();		
		this.eImgContainer.setStyle({
			width: this.newWidth + 'px',
			height: this.newHeight + 'px',
			marginTop: this.newMarginTop + 'px'
		});

        /* Появление блока и фона */
		if (this.conf.animate) {
			new Effect.Parallel([
				new Effect.Appear(this.eOverlay, { to: this.conf.ovOpacity }),
				new Effect.Appear(this.eBody)
			], {
				duration: this.time.appear, 
				afterFinish: this.changeImage.bind(this)
			});
		} else { this.changeImage(); }
	},
	changeImage: function(active)
	{
		if (typeof active == 'number') { this.iActive = active; }
		
		this.eNavPrev.hide();		
		this.eNavNext.hide();
		this.eNav.hide();		
		
		/**/
		if (this.conf.animate) {
			new Effect.Parallel([
				new Effect.Fade(this.eImg),
				new Effect.Fade(this.eImgData),
				new Effect.SlideUp(this.eImgData)
			], {
				duration: this.time.fade,
				afterFinish: this.preloadImage.bind(this)
			});
		} else {
			this.eImg.hide();
			this.eImgData.hide();
			this.preloadImage();
		}
	},
	preloadImage: function()
	{
		this.eNavWait.show();
		var preloader = new Image();
		preloader.onload = (function(){
			this.eNavWait.hide();
			this.eImg.src = this.aImages[this.iActive].href;
			this.newHeight = preloader.height;
			this.newWidth = preloader.width;
			this.calcResizeParams();
			this.resize();
		}).bind(this);
		preloader.src = this.aImages[this.iActive].href;
	},
	resize: function()
	{
		if (this.conf.animate) {
			new Effect.Morph(this.eImgContainer, {
				style: {
					width: this.newWidth + "px"
				},
				duration: this.durResizeWidth,
				afterFinish: (function(){
					new Effect.Morph(this.eImgContainer, {
						style: {
							height:    this.newHeight + "px",
							marginTop: this.newMarginTop + "px"
						},
						duration: this.durResizeHeight,
						afterFinish: this.showElements.bind(this)
					});
				}).bind(this)
			});
		} else { this.showElements(); }
	},
	showElements: function()
	{
		if (this.aImages.length > 1) {
			this.eImgNumber.show().update(this.conf.numTpl.replace('%i', this.iActive+1).replace('%t', this.aImages.length));
		} else { this.eImgNumber.hide(); }
		
		if (this.aImages[this.iActive].title) {
			this.eImgCaption.show().update(this.aImages[this.iActive].title);
		} else { this.eImgCaption.hide(); }
		
		if (this.conf.animate) {
			new Effect.Parallel([
				new Effect.Appear(this.eImg),
				new Effect.Appear(this.eImgData),
				new Effect.SlideDown(this.eImgData)
			], {
				duration: this.time.appear,
				afterFinish: this.updateNavigation.bind(this)
			});
		} else {
			this.updateNavigation();
		}
		
		this.preloadOtherImages();
	},
	updateNavigation: function()
	{
        this.eNav.show();		
		if (this.iActive > 0) { this.eNavPrev.show(); }		
		if (this.iActive < (this.aImages.length - 1)) { this.eNavNext.show(); }
		this.call.onChange(this);
	},
	preloadOtherImages: function()
	{
        var preloadNext, preloadPrev;
        if (this.aImages.length > (this.iActive + 1)){
            preloadNext = new Image();
            preloadNext.src = this.aImages[this.iActive + 1].href;
        }
        if (this.iActive > 0){
            preloadPrev = new Image();
            preloadPrev.src = this.aImages[this.iActive - 1].href;
        }
	},
	end: function(event)
	{
		event.stop();
		
		this.eNav.hide();
		this.eNavWait.hide();
		
		this.newHeight = 0;
		this.newWidth = 0;
		this.calcResizeParams();
		
		if (this.conf.animate){
			new Effect.Parallel([
				new Effect.Fade(this.eImg),
				new Effect.Fade(this.eImgData),
				new Effect.SlideUp(this.eImgData)
			], {
				duration: this.time.fade,
				afterFinish: (function(){
					new Effect.Morph(this.eImgContainer, {
						style: {
							height:    this.newHeight + "px",
							marginTop: this.newMarginTop + "px"
						},
						duration: this.durResizeHeight,
						afterFinish: (function(){
							new Effect.Morph(this.eImgContainer, {
								style: {
									width: this.newWidth + "px"
								},
								duration: this.durResizeWidth,
								afterFinish: (function(){
									new Effect.Parallel([
										new Effect.Fade(this.eBody),
										new Effect.Fade(this.eOverlay)
									], {
										duration: this.time.fade,
										afterFinish: (function(){
											this.eImg.src = '';
											this.eImgNumber.update();
											this.eImgCaption.update().hide();
											this.eNavWait.show();
											$$('select', 'object', 'embed').each(function(node){
												node.setStyle({visibility: 'visible'});
											});
											this.call.onEnd(this);
										}).bind(this)
									});
								}).bind(this)
							});
						}).bind(this)
					});
				}).bind(this)
			});
		} else {
			this.eImg.src = '';
			this.eImg.hide();
			this.eImgData.hide();
			this.eImgNumber.update();
			this.eImgCaption.update().hide();
			this.eNavWait.show();
			$$('select', 'object', 'embed').each(function(node){
				node.setStyle({visibility: 'visible'});
			});
			this.call.onEnd(this);
		}
	}
};
