<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<meta name="google-site-verification" content="x07Ywymh9h7exAMlx60RfHTZJKFicDjy32I8FaQp22U" />
<link rel="SHORTCUT ICON" href="/favicon.png" />
<title><?php echo $this->siteTitle; ?></title>
<link href="/theme/default/css/theme.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/prototype/prototype.js"></script>
<script type="text/javascript" src="/js/scriptaculous/scriptaculous.js?load=effects,builder"></script>

<script type="text/javascript" src="/js/subscribe/js/subscribe.js"></script>

<script type="text/javascript" src="/js/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.queue.js"></script>
<script type="text/javascript" src="/js/swfupload/swfupload.cookies.js"></script>
<script type="text/javascript" src="/js/tiny_mce/plugins/tinybrowser/tb_standalone.js.php"></script>
<script type="text/javascript" src="/js/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>
<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jsGallery/gallery.css" />
<script type="text/javascript" src="/js/jsGallery/gallery.js"></script>
<!-- vkontakte widjets -->
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?23"></script>
<script type="text/javascript" src="http://vkontakte.ru/js/api/share.js?10"></script>
<script type="text/javascript">
    if (typeof VK != 'undefined') {
        VK.init({apiId: 2232115, onlyWidgets: true});
    }
</script>
<!-- /vkontakte widjets -->
</head>
<body>
<div id="lightbox_container">
</div>
<script>
document.observe('dom:loaded', function(){
	new jsGallery("lightbox_container", {
		onInitialize: (function(obj){ /*alert(obj.aImages.length);*/ })
	});
});
</script>
<div class="header">
	<div class="header_resize">
		<div class="header_big_img">
			<img id="header_big_img0" src="/theme/default/img/header_0.jpg" />
			<img id="header_big_img1" src="/theme/default/img/header_1.jpg" />
			<img id="header_big_img2" src="/theme/default/img/header_2.jpg" />
		</div>
		<script>
			function fader(){
				(function(){
					new Effect.Fade("header_big_img2", {duration: 3});
				}).delay(10);
				(function(){
					new Effect.Fade("header_big_img1", {duration: 3});
				}).delay(20);
				(function(){
					new Effect.Appear("header_big_img2", {duration: 3, afterFinish: function(){
						$("header_big_img1").show();
					}});
				}).delay(30);
			}
			fader();
			setInterval('fader()', 40000);
			//document.observe('dom:loaded', function(){ fader(); });
		</script>
		<a class="header_logo" href="/"><img src="/theme/default/img/header_logo.png" /></a>
		<div class="menu">
			<?php echo $this->topMenu; ?>
		</div>
	</div>
</div>
<div class="body">
	<div class="push1"></div>
	<div class="container">
		<div class="col_left">
			<div class="left_mod_title">Меню</div>
			<div class="left_mog_body">
				<div class="left_menu">
					<?php echo $this->leftMenu; ?>
				</div>
			</div>
			<?php if ($this->leftBanner): ?>
			<div class="banner_left">
			<?php echo $this->leftBanner['introtext']; ?>
			</div>
			<?php endif; ?>
			<?php echo $this->left; ?>
        </div>
		<div class="col_center">
			<?php echo $this->center; ?>
			<?php if (!defined('ERROR_404_PAGE')): ?>
            <div class="center_mod_body">
                <div class="col_center_padding" style="text-align:center;">
					<div id="vk_like" style="float:left; clear:none !important;">&nbsp;</div>
					<div style="float:left;"><iframe src="http://www.facebook.com/plugins/like.php?href&amp;layout=button_count&amp;show_faces=true&amp;width=450&amp;action=like&amp;font=tahoma&amp;colorscheme=light&amp;height=21" frameborder="0" scrolling="no" height="21"></iframe></div>
					<div class="clr"></div>
					<div id="vk_comments" style="margin-top:10px;">&nbsp;</div>
					<script type="text/javascript">
					VK.Widgets.Like("vk_like", {type: "button", verb: 0});
					</script>
					<script type="text/javascript">
					VK.Widgets.Comments("vk_comments", {limit: 10, width: "510", attach: "*"});
					</script>
					<div id="fb-root" style="margin-top:10px;"></div>
					<script src="http://connect.facebook.net/ru_RU/all.js#appId=APP_ID&amp;xfbml=1"></script>
					<fb:comments href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" num_posts="5" width="510"></fb:comments>
        		</div>
        	</div>
        	<?php endif; ?>
		</div>
		<div class="col_right">
			<?php echo $this->adminBlock; ?>
			<div class="right_mod_title" style="padding-top:4px; height:36px;">Подписка на новости, события и акции</div>
			<div class="right_mog_body">
			    <div id="subscribe"></div>
			    <script>
			        new Subscribe("subscribe", {
				        labelText:'Эл. почта',
				        buttonText: 'Подписаться',
				        encoding: 'cp1251',
				        ERROR_EMPTY: 'Введите адрес эл. почты',
					});
			    </script>
			</div>
			<?php if ($this->rightBanner): ?>
			<div class="banner_right">
			<?php echo $this->rightBanner['introtext']; ?>
			</div>
			<?php endif; ?>
			<?php echo $this->right; ?>
            <div class="right_mod_title">Поиск</div>
			<div class="right_mog_body">
                <form action="http://mirmafii.com.ua/search" id="cse-search-box">
                <input type="hidden" name="cx" value="015907517302530281162:-i6txmwcc7m" />
                <input type="hidden" name="cof" value="FORID:10" />
                <input type="hidden" name="ie" value="cp1251" />
                <table id="search" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="155">
                        	<input id="searchText" type="text" name="q" size="10" />
                        </td>
                    </tr>
                    <tr>
                        <td><input id="searchButton" class="submit" type="submit" value="Искать" /></td>
                    </tr>
                </table>
                </form>
                <script type="text/javascript" src="//www.google.com/jsapi"></script>
                <script type="text/javascript">google.load("elements", "1", {packages: "transliteration"});</script>
                <script type="text/javascript" src="//www.google.com/cse/t13n?form=cse-search-box&t13n_langs=ru"></script>
                <script type="text/javascript" src="//www.google.com/cse/brand?form=cse-search-box&lang=ru"></script>
			</div>
        </div>
		<div class="clr"></div>
	</div>
	<div class="push2"></div>
</div>
<div class="footer">
	<div class="footer_resize">
		<div class="bigmir_counter">
            <!--bigmir)net TOP 100-->
            <script type="text/javascript" language="javascript"><!--
            function BM_Draw(oBM_STAT){
            document.write('<table cellpadding="0" cellspacing="0" border="0" style="display:inline;margin-right:4px;"><tr><td><div style="font-family:Tahoma;font-size:10px;padding:0px;margin:0px;"><div style="width:7px;float:left;background:url(\'http://i.bigmir.net/cnt/samples/default/b52_left.gif\');height:17px;padding-top:2px;background-repeat:no-repeat;"></div><div style="float:left;background:url(\'http://i.bigmir.net/cnt/samples/default/b52_center.gif\');text-align:left;height:17px;padding-top:2px;background-repeat:repeat-x;"><a href="http://www.bigmir.net/" target="_blank" style="color:#0000ab;text-decoration:none;">bigmir<span style="color:#ff0000;">)</span>net</a>&nbsp;&nbsp;<span style="color:#797979;">хиты</span>&nbsp;<span style="color:#003596;font:10px Tahoma;">'+oBM_STAT.hits+'</span>&nbsp;<span style="color:#797979;">хосты</span>&nbsp;<span style="color:#003596;font:10px Tahoma;">'+oBM_STAT.hosts+'</span></div><div style="width:7px;float: left;background:url(\'http://i.bigmir.net/cnt/samples/default/b52_right.gif\');height:17px;padding-top:2px;background-repeat:no-repeat;"></div></div></td></tr></table>');
            }
            //-->
            </script>
            <script type="text/javascript" language="javascript"><!--
            bmN=navigator,bmD=document,bmD.cookie='b=b',i=0,bs=[],bm={o:1,v:16884267,s:16884267,t:0,c:bmD.cookie?1:0,n:Math.round((Math.random()* 1000000)),w:0};
            for(var f=self;f!=f.parent;f=f.parent)bm.w++;
            try{if(bmN.plugins&&bmN.mimeTypes.length&&(x=bmN.plugins['Shockwave Flash']))bm.m=parseInt(x.description.replace(/([a-zA-Z]|\s)+/,''));
            else for(var f=3;f<20;f++)if(eval('new ActiveXObject("ShockwaveFlash.ShockwaveFlash.'+f+'")'))bm.m=f}catch(e){;}
            try{bm.y=bmN.javaEnabled()?1:0}catch(e){;}
            try{bmS=screen;bm.v^=bm.d=bmS.colorDepth||bmS.pixelDepth;bm.v^=bm.r=bmS.width}catch(e){;}
            r=bmD.referrer.slice(7);if(r&&r.split('/')[0]!=window.location.host){bm.f=escape(r);bm.v^=r.length}
            bm.v^=window.location.href.length;for(var x in bm) if(/^[ovstcnwmydrf]$/.test(x)) bs[i++]=x+bm[x];
            bmD.write('<sc'+'ript type="text/javascript" language="javascript" src="http://c.bigmir.net/?'+bs.join('&')+'"></sc'+'ript>');
            //-->
            </script>
            <noscript>
            <a href="http://www.bigmir.net/" target="_blank"><img src="http://c.bigmir.net/?v16884267&s16884267&t2" width="88" height="31" alt="bigmir)net TOP 100" title="bigmir)net TOP 100" border="0" /></a>
            </noscript>
            <!--bigmir)net TOP 100-->
        </div>
    </div>	
</div>
</body>
</html>
