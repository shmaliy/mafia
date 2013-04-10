// JavaScript Document
var swfu;
/*var gpath;

var uploader = {
	basedir : 'contents',
	path : '',
	dirlist : function(l, level){
		if (level == 0){
			$('folderlist').innerHTML = '';
			var s = Builder.node('select');
			s.className = 'folders';
			s.onchange = function(){ uploader.set_root(this.value); };
			s.id = 'flist';
			$('folderlist').appendChild(s);			
			
			var o = Builder.node('option');
			o.setAttribute("value", this.basedir);
			o.text = '/';
			$('flist').appendChild(o);
		}
		
		for(var i in l){
			var o = Builder.node('option');
			o.setAttribute("value", l[i]["fn"]);
			o.text = '/' + l[i]["fn"].replace(this.basedir+'/','');
			$('flist').appendChild(o);
			if (l[i]["fc"]){ this.dirlist(l[i]["fc"], level+1); }
		}
	},
	dirselect : function(request){
		uploader.dirlist(request.responseText.evalJSON(true), 0);
		for (var i=0; i < $('flist').options.length; i++){
			if ($('flist').options[i].value.toString() == this.path){
				$('flist').options[i].selected = true;
			}
		}
	},
	destroy : function(){
		swfu.destroy();
		$('swfupload').innerHTML = '<span id="spanButtonPlaceHolder"></span>';
	},
	set_root : function(path){
		new Ajax.Request(
			'/cms/index.php',
			{
				method: 'post',
				parameters: { func: 'fm::set_root', funca: path },
				onComplete: function(){
					uploader.destroy();
					uploader.init(path);
				},
				onSucess: function(){}
			}
		);		
	},
	ftype : function(fname, path){
		var t = fname.substr(-4);
		switch(t){
			case '.avi': return ['avi'];
			case '.flv': return ['flv'];
			case '.css': return ['css'];
			case '.tpl': return ['tpl'];
			case '.zip': return ['zip'];
			case '.png': return ['','/cms/image.php?i=../'+path+'/'+fname+'&t=png&m=fit&w=50&h=50'];
			case '.jpg': return ['','/cms/image.php?i=../'+path+'/'+fname+'&t=png&m=fit&w=50&h=50&bg=FFFFFF'];
			case '.gif': return ['','/cms/image.php?i=../'+path+'/'+fname+'&t=png&m=fit&w=50&h=50'];
			default: return ['file'];
		}
	},
	filelist : function(flist, path){
		var L = $('filelist');
		L.innerHTML = '';
		if (this.basedir != this.path){
			var f = Builder.node('a');
			f.className = 'file_i folder';
			var p = this.path.split('/');
			var p2 = new Array();
			for (var j=0; j<p.length-1; j++){ p2[p2.length] = p[j]; }
			p2 = p2.join('/');
			f.setAttribute("onclick", "uploader.set_root('" + p2 + "');");
			f.innerHTML = '<b>...</b>';
			L.appendChild(f);
		}
		for (var i in flist){
			var f = Builder.node('a');
			var e = flist[i];
			t = uploader.ftype(e["fn"], this.path);
			if (e["ft"] == 'fld'){
				f.className = 'file_i folder';
				f.setAttribute("onclick", "uploader.set_root('" + this.path + '/' + e["fn"] + "');");
			}else{
				f.className = 'file_i ' + t[0];
				if (t[1]){
					//f.href = '/cms/image.php?i=../' + this.path + '/' + e["fn"] + '&t=png&m=side&s=500&sm=long';
					//f.setAttribute("rel", "lightbox[]");
				}
			}
			if (t[1]){ f.style.backgroundImage = 'url('+t[1]+')'; }
			f.innerHTML = e["fn"];
			L.appendChild(f);
		}
		var f = Builder.node('div');
		f.className = 'clr';
		L.appendChild(f);
	},
	init : function(path){
		this.path = path;
		var settings = {
			flash_url : "/cms/swf/swfupload.swf",
			prevent_swf_caching : false,
			upload_url : "/cms/upload.php",
			post_params : {"path" : this.path},
			
			file_post_name : "userfile",
			file_size_limit : "100 MB",
			file_types : "*.*",
			file_types_description : "All Files",
			file_upload_limit : 100,
			file_queue_limit : 0,
			
			custom_settings : { progressTarget : "fsUploadProgress", cancelButtonId : "u_btn_cancel" },
			debug: false,
			
			button_image_url: "/cms/swf/u_btn_browse.png",
			button_width: '32',
			button_height: '32',
			button_placeholder_id: "spanButtonPlaceHolder",
			button_text_style: "",
			button_text_left_padding: 0,
			button_text_top_padding: 0,
			
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			queue_complete_handler : queueComplete	// Queue plugin event		
		};
		swfu = new SWFUpload(settings);
		
		new Ajax.Request('/cms/index.php', {
				method: 'post',
				parameters: { func: 'fm::dirtree' },
				onComplete: function(request){ uploader.dirselect(request); },
				onSucess: function(){}
			}
		);
		
		new Ajax.Request('/cms/index.php', {
				method: 'post',
				parameters: { func: 'fm::flist' },
				onComplete: function(request){ uploader.filelist(request.responseText.evalJSON(true), path); },
				onSucess: function(){}
			}
		);
	}
};*/
var fileManager = {
	base : 'contents',
	path : '',
	initialize : function(path){
		this.path = path;		
		var parent = $('fm');
		parent.appendChild(Builder.node('form', { class : 'upload' }, [
			Builder.node('table', { cellpadding : '0', cellspacing : '0' }, [
				Builder.node('tr', null, [
					Builder.node('td', { class : 'browse' }, 
						Builder.node('div', { id : 'swfupload' }, Builder.node('span', { id : 'FM_browse_btn' }))
					),
					Builder.node('td', { class : 'cancel' }, 
						Builder.node('input', { id : 'FM_btn_cancel', type : 'image', src : '/cms/swf/u_btn_cancel.png', onclick : 'swfu.cancelQueue();', disabled : 'disabled' })
					),
					Builder.node('td', { class : 'ititle' }, [ 'Очередь загрузки',
						Builder.node('div', { id : 'divStatus' }, '0 Files Uploaded')
					]),
					Builder.node('td', { id : 'folderlist' }),
					Builder.node('td', { class : 'add' }, Builder.node('a', { href : '#' }))
				])
			])
		]));
		parent.appendChild(Builder.node('div', {id : 'FM_upload_progress'}, '11'));
		parent.appendChild(Builder.node('div', {id : 'FM_filelist'}, '11'));
		this.folders_s();
		/*var settings = {
			flash_url : "/cms/swf/swfupload.swf",
			prevent_swf_caching : false,
			upload_url : "/cms/upload.php",
			post_params : {"path" : this.path},
			
			file_post_name : "userfile",
			file_size_limit : "100 MB",
			file_types : "*.*",
			file_types_description : "All Files",
			file_upload_limit : 100,
			file_queue_limit : 0,
			
			custom_settings : { progressTarget : "FM_upload_progress", cancelButtonId : "FM_btn_cancel" },
			debug: false,
			
			button_image_url: "/cms/swf/u_btn_browse.png",
			button_width: '32',
			button_height: '32',
			button_placeholder_id: "FM_browse_btn",
			button_text_style: "",
			button_text_left_padding: 0,
			button_text_top_padding: 0,
			
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			queue_complete_handler : queueComplete	// Queue plugin event		
		};
		swfu = new SWFUpload(settings);*/
	},
	folders_s : function(){
		new Ajax.Request('/cms/index.php', { method : 'post',
				parameters : { func : 'fm::folders_s' },
				onComplete : function(resp){
					var req = resp.responseText.evalJSON(true);
					var ret = '<select class="folders" onchange="fileManager.s_root(this.value);"><option value="' + fileManager.base + '">/</option>';
					for (var i=0; i<req.length; i++){
						ret += '<option value="' + req[i]['v'] + '"';
						ret += (req[i]['v'] == this.path) ? ' selected="selected"' : '';
						ret += '>/' + req[i]['v'] + '</option>';
					}					
					ret += '</select>';
					$('folderlist').innerHTML = ret;					
				},
				onFailure : function(){ $('folderlist').innerHTML = 'error'; }
			}
		);
	}
};

