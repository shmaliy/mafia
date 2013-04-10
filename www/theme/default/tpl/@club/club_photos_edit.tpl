<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_photos = {' . implode(',', $this->constants) . '}';
}
?></script>
<?php if (!empty($this->item)): ?>
<div class="center_mod_title">Редактирование фотоальбома</div>
<?php else: ?>
<div class="center_mod_title">Добавление фотоальбома</div>
<?php endif; ?>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
    <form name="create_photos_form" action="#" method="post" enctype="multipart/form-data" onsubmit="return create(this,event);">
    <input type="hidden" name="function" value="club_photos->photosSaveItem" />
    <input type="hidden" name="id" value="<?php echo $this->item['id']; ?>" />
    <table class="form" cellpadding="0" cellspacing="0">
        <tr>
            <td class="col1">Название</td>
            <td class="col2" colspan="2"><input type="text" name="title" value="<?php echo $this->item['title']; ?>" /></td>
        </tr>
        <tr>
            <td class="col1">Обложка</td>
            <td class="col2">
                <input type="hidden" name="image" value="<?php echo $this->item['image']; ?>" />
                <?php if (!empty($this->item)): ?>
                <div id="linkImage" style="display:block;">
                <?php else: ?>
                <div id="linkImage" style="display:none;">
                <?php endif; ?>
                    <div class="icon"><img src="/image.php?i=<?php echo substr($this->item['image'], 1); ?>&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7" />
                        <div class="edit_actions">
                            <a onclick="delImage(document.create_photos_form.image.value);"><img src="/theme/default/img/delete.png" /></a>
                        </div>
                    </div>
                </div>
			</td>
            <td class="col3">
				<div class="upload" style="position:relative !important;">
                	<input id="uploadImage" class="submit" type="button" value="Загрузить" />
					<div class="uploader_placeholder"><div id="uploader1"></div></div>
				</div>
            </td>
        </tr>
        <tr>
            <td class="col1"><input type="hidden" name="images" value="<?php echo $this->item['images']; ?>" /></td>
            <td><?php if (!empty($this->item)): ?>
				<div class="upload2" style="position:relative !important;">
	            	<input id="uploadImages" class="submit" style="width:218px;" type="button" value="Добавить фотографии" />
					<div class="uploader_placeholder"><div id="uploader2">1</div></div>
				</div>
            <?php endif; ?></td>
            <td class="col3"><input id="create_photos_form_submit" class="submit" type="submit" value="Сохранить" /></td>
        </tr>
        <tr>
            <td class="col1"></td>
            <td colspan="2">Максимальный размер файла: <?php echo ini_get('upload_max_filesize'); ?></td>
        </tr>
        <tr>
            <td colspan="3"><div class="stat" id="create_photos_stat"></div></td>
        </tr>
    </table>
    </form>
	<?php if (!empty($this->item)): ?>
	<div class="col_center_padding">
		<div class="submod_body">
			<div class="photos" id="photosLink">
                <?php if(!empty($this->item['images'])): ?>
                <?php $images = explode('|', $this->item['images']); ?>
                <?php foreach($images as $item): ?>
                <div class="photos_list_i"><img src="/image.php?i=<?php echo substr($item, 1); ?>&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7" />
                    <?php if($this->user && $this->user['email'] == $this->club['title_alias']): ?>
                    <div class="edit_actions">
                        <a onclick="delImages('<?php echo $item; ?>');"><img src="/theme/default/img/delete.png" /></a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <div class="clr"></div>
                <?php else: ?>
                <div class="empty">Нет фотографий</div>
                <?php endif; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>
<script>
    function create(data, event)
    {
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
                document.create_photos_form.title.setStyle({outline:"none"});
                $("uploadImage").setStyle({outline:"none"});
                if ($("uploadImages")) {
                    $("uploadImages").setStyle({outline:"none"});
                }
                document.create_photos_form.disable();
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
                'function': 'club_photos->photosDeleteImage',
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
	
	var interval1;
	var uploader1 = new SWFUpload({
		flash_url : "/js/swfupload/swfupload.swf",
		prevent_swf_caching : false,
		upload_url : "/index.php",
		post_params : {
			'function': 'club_photos->photosUploadImage',
			'itemId': '<?php echo $this->item['id']; ?>',
			'clubId': '<?php echo $this->club['id']; ?>',
			'catId': '<?php echo $this->cat['id']; ?>'
		},
		
		file_post_name : "userfile",
		file_size_limit : "10 MB",
		file_types : "*.jpg;*.png;*.gif",
		file_types_description : "Изображения",
		file_upload_limit : 0,
		file_queue_limit : 1,
		
		debug: false,
		
		button_image_url: "/js/swfupload/swfupload.png",
		button_width: $("uploadImage").getWidth(),
		button_height: $("uploadImage").getHeight(),
		button_placeholder_id: "uploader1",
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
				interval1 = window.setInterval(function(){
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
			window.clearInterval(interval1);
			$("uploadImage").value = "Загрузить";
		}
	});	
	function updateImages(image)
	{
		string = document.create_photos_form.images.value;
		if (string != '') {
			var exists = string.split('|');
		} else { var exists = []; }
		var arr = [];
		var insert = true;
		for (var i=0; i<exists.length; i++) {
			arr[arr.length] = exists[i];
			if (image && image != '' && exists[i] == image) {
				insert = false;
			}
		}
			
		if (image && image != '') {
			if (insert == true) {
				arr[arr.length] = image;
			} else {
				window.setTimeout(function(){$("create_photos_stat").update("<div class=\"ststus ststus_sucess\">" + club_photos.ERROR_FILE_EXISTS + "</div>")}, 200);
			}
		}
		document.create_photos_form.images.value = arr.join('|');
		$("photosLink").update('');
		for (var i=0; i<arr.length; i++) {
			$("photosLink").insert('<div class="photos_list_i">'
				+ '<img src="/image.php?i='
				+ arr[i].toString().substr(1)
				+ '&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7" />'
				+ '<div class="edit_actions">'
				+ '<a onclick="delImages('
				+ "'" + arr[i].toString() + "'" + ');">'
				+ '<img src="/theme/default/img/delete.png" />'
				+ '</a>'
				+ '</div>'
				+ '</div>'
			);
		}
		$("photosLink").insert('<div class="clr"></div>');
		if (arr.length == 0) { $("photosLink").update('<div class="empty">Нет фотографий</div>'); }
	}

	function delImages(image)
	{
		string = document.create_photos_form.images.value;
		if (image != '') {
			if (string != '') {
				var exists = string.split('|');
			} else { var exists = []; }
			var arr = [];
			var del = false;
			for (var i=0; i<exists.length; i++) {
				if (exists[i] == image) {
					del = true;
				} else {
					arr[arr.length] = exists[i];
				}
			}
			if (del == true) {
				new Ajax.Request('/index.php', {
					method: 'post',
					parameters: {
						'function': 'club_photos->photosDeleteImages',
						'itemId': '<?php echo $this->item['id']; ?>',
						'clubId': '<?php echo $this->club['id']; ?>',
						'catId': '<?php echo $this->cat['id']; ?>',
						'image': image
					},
					encoding: 'UTF-8',
					onCreate : (function(){}).bind(this),
					onComplete : (function(request){
						var state;
						eval(request.responseText);
						if (state == club_photos.SUCESS_DELETE_FILE) {
							document.create_photos_form.images.value = arr.join('|');
							updateImages();
						}
					}).bind(this)
				});        
			}
		}
	}
	
    if ($("uploadImages")) {
        var interval2;
        var uploader2 = new SWFUpload({
            flash_url : "/js/swfupload/swfupload.swf",
            prevent_swf_caching : false,
            upload_url : "/index.php",
            post_params : {
                'function': 'club_photos->photosUploadImages',
                'itemId': '<?php echo $this->item['id']; ?>',
                'clubId': '<?php echo $this->club['id']; ?>',
                'catId': '<?php echo $this->cat['id']; ?>'
            },
            
            file_post_name : "userfile",
            file_size_limit : "2 MB",
            file_types : "*.jpg;*.png;*.gif",
            file_types_description : "Изображения",
            file_upload_limit : 0,
            file_queue_limit : 20,
            
            debug: false,
            
            button_image_url: "/js/swfupload/swfupload.png",
            button_width: $("uploadImages").getWidth(),
            button_height: $("uploadImages").getHeight(),
            button_placeholder_id: "uploader2",
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
                    interval2 = window.setInterval(function(){
                        if ($("uploadImages").value.length < 11){
                            $("uploadImages").value += '.';                  
                        } else {
                            $("uploadImages").value = "Загрузка";
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
                window.clearInterval(interval2);
                $("uploadImages").value = "Добавить фотографии";
            }
        }); 
    }
</script>
