<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_videos = {' . implode(',', $this->constants) . '}';
}
?></script>
<?php if (!empty($this->item)): ?>
<div class="center_mod_title">Редактирование видеозаписи</div>
<?php else: ?>
<div class="center_mod_title">Добавление видеозаписи</div>
<?php endif; ?>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
    <form name="create_videos_form" action="#" method="post" enctype="multipart/form-data" onsubmit="return create(this,event);">
    <input type="hidden" name="function" value="club_videos->videosSaveItem" />
    <input type="hidden" name="id" value="<?php echo $this->item['id']; ?>" />
    <table class="form" cellpadding="0" cellspacing="0">
        <tr>
            <td class="col1">Название</td>
            <td class="col2" colspan="2"><input type="text" name="title" value="<?php echo $this->item['title']; ?>" /></td>
        </tr>
        <tr>
            <td class="col1">Стопкадр</td>
            <td class="col2">
                <input type="hidden" name="image" value="<?php echo $this->item['image']; ?>" />
                <?php if (!empty($this->item)): ?>
                <div id="linkImage" style="display:block;">
                <?php else: ?>
                <div id="linkImage" style="display:none;">
                <?php endif; ?>
                    <div class="icon"><img src="/image.php?i=<?php echo substr($this->item['image'], 1); ?>&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7" />
                        <div class="edit_actions">
                            <a onclick="delImage(document.create_videos_form.image.value);"><img src="/theme/default/img/delete.png" /></a>
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
            <td colspan="3">Код YouTube (ширина не более 500px включительно)</td>
        </tr>
        <tr>
            <td colspan="3"><textarea name="introtext" style="height:100px;"><?php echo $this->item['introtext']; ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td class="col3"><input id="create_videos_form_submit" class="submit" style="width:110px;" type="submit" value="Сохранить" /></td>
        </tr>
        <tr>
            <td colspan="3"><div class="stat" id="create_videos_stat"></div></td>
        </tr>
    </table>
    </form>
</div>
<script>
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
                document.create_videos_form.title.setStyle({outline:"none"});
                $("uploadImage").setStyle({outline:"none"});
                document.create_videos_form.introtext.setStyle({outline:"none"});
                document.create_videos_form.disable();
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
                'function': 'club_videos->videosDeleteImage',
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
			'function': 'club_videos->videosUploadImage',
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
