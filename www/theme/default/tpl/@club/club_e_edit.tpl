<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club = {' . implode(',', $this->constants) . '}';
}
?></script>
<?php if (!empty($this->item)): ?>
<div class="center_mod_title">Редактирование клуба</div>
<?php else: ?>
<div class="center_mod_title">Добавление клуба</div>
<?php endif; ?>
<div class="center_mod_body">
    <form name="create_club_form" action="#" method="post" enctype="multipart/form-data" onsubmit="return create(this,event);">
    <input type="hidden" name="function" value="club->clubSaveItem" />
    <input type="hidden" name="id" value="<?php echo $this->item['id']; ?>" />
    <table class="form" cellpadding="0" cellspacing="0">
        <tr>
            <td class="col1">Название</td>
            <td class="col2" colspan="2"><input type="text" name="title" value="<?php echo $this->item['title']; ?>" /></td>
        </tr>
        <?php if (!empty($this->item)): ?>
        <tr>
            <td class="col1">Изображение</td>
            <td class="col2">
                <input type="hidden" name="image" value="<?php echo $this->item['image']; ?>" />
                <?php if (!empty($this->item) && $this->item['image'] != ''): ?>
                <div id="linkImage" style="display:block;">
                <?php else: ?>
                <div id="linkImage" style="display:none;">
                <?php endif; ?>
                    <div class="icon"><img src="/image.php?i=<?php echo substr($this->item['image'], 1); ?>&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7" />
                        <div class="edit_actions">
                            <a onclick="delImage(document.create_club_form.image.value);"><img src="/theme/default/img/delete.png" /></a>
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
        <?php endif; ?>
        <tr>
            <td colspan="3">Описание</td>
        </tr>
        <tr>
            <td colspan="3"><textarea name="description" style="height:300px;"><?php echo $this->item['description']; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td class="col3"><input class="submit" style="width:110px;" type="submit" value="Сохранить" /></td>
        </tr>
        <tr>
            <td colspan="3"><div class="stat" id="create_club_stat"></div></td>
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
        if ($("description_parent")) {
        	tinyMCE.triggerSave();
        }
        event.stop();
        if (data.tagName.toUpperCase() == 'FORM'){
            var data = data.serialize(true);
        }
        
        new Ajax.Request('/index.php', {
            method: 'post',
            parameters: data,
            encoding: 'UTF-8',
            onCreate : (function(){
                document.create_club_form.title.setStyle({outline:"none"});
                if ($("uploadImage")) {
                    $("uploadImage").setStyle({outline:"none"});
                }
                $("description_parent").setStyle({outline:"none"}); // for mce editor field
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
                'function': 'club->clubDeleteImage',
                'itemId': '<?php echo $this->item['id']; ?>',
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
			'function': 'club->clubUploadImage',
			'itemId': '<?php echo $this->item['id']; ?>',
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
