<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_afishi = {' . implode(',', $this->constants) . '}';
}
?></script>
<?php if (!empty($this->item)): ?>
<div class="center_mod_title">Редактирование афиши</div>
<?php else: ?>
<div class="center_mod_title">Добавление афиши</div>
<?php endif; ?>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
    <form name="create_afishi_form" action="#" method="post" enctype="multipart/form-data" onsubmit="return create(this,event);">
    <input type="hidden" name="function" value="club_afishi->afishiSaveItem" />
    <input type="hidden" name="id" value="<?php echo $this->item['id']; ?>" />
    <table class="form" cellpadding="0" cellspacing="0">
        <tr>
            <td class="col1">Название</td>
            <td class="col2" colspan="2"><input type="text" name="title" value="<?php echo $this->item['title']; ?>" /></td>
        </tr>
        <tr>
            <td class="col1">Место проведения</td>
            <td class="col2" colspan="2"><input type="text" name="param1" value="<?php echo $this->item['param1']; ?>" /></td>
        </tr>
        <tr>
            <td class="col1">Изображение</td>
            <td class="col2">
                <input type="hidden" name="image" value="<?php echo $this->item['image']; ?>" />
                <?php if (!empty($this->item)): ?>
                <div id="linkImage" style="display:block;">
                <?php else: ?>
                <div id="linkImage" style="display:none;">
                <?php endif; ?>
                    <div class="icon"><img src="/image.php?i=<?php echo substr($this->item['image'], 1); ?>&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7" />
                        <div class="edit_actions">
                            <a onclick="delImage(document.create_afishi_form.image.value);"><img src="/theme/default/img/delete.png" /></a>
                        </div>
                    </div>
                </div>
			</td>
            <td class="col3">
				<div class="upload" style="position:relative !important;">
                	<input id="uploadImage" class="submit" type="button" value="Загрузить" />
					<div class="uploader_placeholder"><div id="uploader"></div></div>
				</div>
            </td>
        </tr>
        <tr>
            <td class="col1">Дата</td>
            <td class="col2" colspan="2">
                <select class="year" name="d_year">
                <?php $cYear = !empty($this->item) ? date("Y", $this->item['publish_up']) : date("Y"); ?>
                <?php for ($y=date("Y")-5; $y<=date("Y")+5; $y++): ?>
                    <?php if ($y == $cYear): ?>
                    <option value="<?php echo $y; ?>" selected="selected"><?php echo $y; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
                </select><select class="month" name="d_month">
                <?php $cMonth = !empty($this->item) ? date("m", $this->item['publish_up']) : date("m"); ?>
                <?php foreach($this->month as $month => $monthText): ?>
                    <?php if ($month == $cMonth): ?>
                    <option value="<?php echo $month; ?>" selected="selected"><?php echo $monthText ?></option>
                    <?php else: ?>
                    <option value="<?php echo $month; ?>"><?php echo $monthText ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
                </select><select class="day" name="d_day">
                <?php $cDay = !empty($this->item) ? date("d", $this->item['publish_up']) : date("d"); ?>
                <?php for($d=1; $d<=31; $d++): ?>
                    <?php if ($d == $cDay): ?>
                    <option value="<?php echo $d; ?>" selected="selected"><?php echo $d; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="col1">Время</td>
            <td class="col2" colspan="2">
                <select class="hour" name="d_hour">
                <?php $cHr = !empty($this->item) ? date("H", $this->item['publish_up']) : date("H"); ?>
                <?php for ($h=0; $h<24; $h++): ?>
                    <?php if ($h < 10){ $dh = "0$h"; } else { $dh = "$h"; } ?>
                    <?php if ($h == $cHr): ?>
                    <option value="<?php echo $h; ?>" selected="selected"><?php echo $dh; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $h; ?>"><?php echo $dh; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
                </select><strong>:</strong><select class="minute" name="d_minute">
                <?php $cMin = !empty($this->item) ? date("i", $this->item['publish_up']) : date("i"); ?>
                <?php for($m=0; $m<60; $m++): ?>
                    <?php if ($m < 10){ $dm = "0$m"; } else { $dm = "$m"; } ?>
                    <?php if ($m == $cMin): ?>
                    <option value="<?php echo $m; ?>" selected="selected"><?php echo $dm; ?></option>
                    <?php else: ?>
                    <option value="<?php echo $m; ?>"><?php echo $dm; ?></option>
                    <?php endif; ?>
                <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3">Описание</td>
        </tr>
        <tr>
            <td colspan="3"><textarea name="introtext" style="height:100px;"><?php echo $this->item['introtext']; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td class="col3"><input id="create_afishi_form_submit" class="submit" style="width:110px;" type="submit" value="Сохранить" /></td>
        </tr>
        <tr>
            <td colspan="3"><div class="stat" id="create_afishi_stat"></div></td>
        </tr>
    </table>
    </form>
</div>
<script>
    tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        skin : "o2k7",
        skin_variant : "silver",
        file_browser_callback : "tinyBrowser",
        forced_root_block : false,
        force_br_newlines : true,
        force_p_newlines : false,
        theme_advanced_resizing_use_cookie : false,
        theme_advanced_resizing : false,
        language : "ru",
        extended_valid_elements : "iframe[name|src|framespacing|border|frameborder|scrolling|title|height|width],object[declare|classid|codebase|data|type|codetype|archive|standby|height|width|usemap|name|tabindex|align|border|hspace|vspace]",
        
        plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
    
        // Theme options
        theme_advanced_buttons1 : "newdocument,|,cut,copy,paste,pastetext,pasteword,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,formatselect,fontsizeselect",
        theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,image,cleanup,code,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell",
        theme_advanced_buttons3 : "tablecontrols,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,|,fullscreen",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
    
        // Example content CSS (should be your site CSS)
        content_css : "/theme/default/css/theme_mce.css",
    
        // Replace values for the template plugin
        template_replace_values : {
            username : "Some User",
            staffid : "991234"
        }
    });
    function create(data, event)
    {
        if ($("introtext_parent")) {
        	tinyMCE.triggerSave();
        }
        event.stop();
        if (data.tagName.toUpperCase() == 'FORM'){
            var data = data.serialize(true);
        }
        data['clubId'] = '<?php echo $this->club['id']; ?>';
        data['catId'] = '<?php echo $this->cat['id']; ?>';

        
        new Ajax.Request('/index.php', {
            method: 'post',
            parameters: data,
            encoding: 'UTF-8',
            onCreate : (function(){
                document.create_afishi_form.title.setStyle({outline:"none"});
                $("uploadImage").setStyle({outline:"none"});
                $("introtext_parent").setStyle({outline:"none"});
                document.create_afishi_form.disable();
            }).bind(this),
            onComplete : (function(request){
                eval(request.responseText);
            }).bind(this)
        });
        return false;
    }   

    function delImage(img)
    {
        new Ajax.Request('/index.php', {
            method: 'post',
            parameters: {
                'function': 'club_afishi->afishiDeleteImage',
                'itemId': '<?php echo $this->item['id']; ?>',
                'clubId': '<?php echo $this->club['id']; ?>',
                'catId': '<?php echo $this->cat['id']; ?>',
                'image': img
            },
            encoding: 'UTF-8',
            onCreate : (function(){}).bind(this),
            onComplete : (function(request){
                eval(request.responseText);
            }).bind(this)
        });        
    }
	
	var interval;
	new SWFUpload({
		flash_url : "/js/swfupload/swfupload.swf",
		prevent_swf_caching : false,
		upload_url : "/index.php",
		post_params : {
			'function': 'club_afishi->afishiUploadImage',
			'itemId': '<?php echo $this->item['id']; ?>',
			'clubId': '<?php echo $this->club['id']; ?>',
			'catId': '<?php echo $this->cat['id']; ?>'
		},
		
		file_post_name : "userfile",
		file_size_limit : "2 MB",
		file_types : "*.jpg;*.png;*.gif",
		file_types_description : "Изображения",
		file_upload_limit : 0,
		file_queue_limit : 1,
		
		debug: false,
		
		button_image_url: "/js/swfupload/swfupload.png",
		button_width: $("uploadImage").getWidth(),
		button_height: $("uploadImage").getHeight(),
		button_placeholder_id: "uploader",
		button_text: '',
		button_text_style: "",
		button_text_left_padding: 0,
		button_text_top_padding: 0,
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		
		file_queued_handler: function(file){},
		file_queue_error_handler: function(){},
		file_dialog_complete_handler: function(numSelected, numQueued){
			try {
				this.startUpload();
			} catch (ex){ this.debug(ex); }
		},
		upload_start_handler: function(file){
			try {
				/* Инициализация индикатора загрузки */
				interval = window.setInterval(function(){
					if ($("uploadImage").value.length < 11){
						$("uploadImage").value += '.';					
					} else {
						$("uploadImage").value = "Загрузка";
					}
				}, 500);
			}
			catch (ex) {}			
			return true;
		},
		upload_progress_handler: function(file, bLoaded, bTotal){},
		upload_error_handler: function(){},
		upload_success_handler: function(file, serverData){
			eval(serverData);
		},
		upload_complete_handler: function(file){
			window.clearInterval(interval);
			$("uploadImage").value = "Загрузить";
		}
	});	
</script>
