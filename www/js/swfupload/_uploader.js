/* AJAX FILE MANAGER 1.0 */
var swfu;
var $FM = {
	base : 'contents',
	path : '',
	returnf : '',
	count : 'Загружено 0',
	total_size : 0,
	total_loaded : 0,
	init : function(path){
		this.path = path;
		this.build();
		this.u_folders_s();
		this.u_folders_list();
		
		var settings = {
			flash_url : "/cms/swf/swfupload.swf",
			prevent_swf_caching : false,
			upload_url : "/cms/upload.php",
			post_params : {"path" : this.path},
			
			file_post_name : "userfile",
			file_size_limit : "20000 MB",
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
			button_window_mode:"opaque",
			
			file_queued_handler : this.swfFileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : this.swfDialogComplete,
			upload_start_handler : this.swfUploadStart,
			upload_progress_handler : this.swfUploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : this.swfUploadSuccess,
			upload_complete_handler : this.swfComplete,
			queue_complete_handler : this.swfQueueComplete	// Queue plugin event		
		};
		swfu = new SWFUpload(settings);
	},
	build : function(){
		var ph = $('FM');
		ph.innerHTML = '';
		ph.appendChild($CE.node('form', {id:'f', name:'upload', action:'/cms/upload.php', method:'post', enctype:'multipart/form-data'},[
			$CE.node('div', {id:'swfupload'}, $CE.node('span', {id:'FM_browse_btn'})),
			$CE.node('input', {id:'FM_btn_cancel', type:'image', src:'/cms/swf/u_btn_cancel.png', disabled:'disabled'}),
			$CE.node('div', {id:'FM_ititle'}, [
				'Очередь загрузки',
				$CE.node('div', {id:'divStatus'}, this.count)
			]),
			$CE.node('div', {id:'FM_add_folder'}, [
				$CE.node('a', {id:'FM_add_folder_f_btn'}),
				$CE.node('div', {id:'FM_add_folder_f'}, [
					$CE.node('input', {id:'FM_folder_n'}),
					$CE.node('img', {src:'/cms/swf/u_btn_ssave.png'}),
					$CE.node('img', {id:'FM_add_folder_f_close', src:'/cms/swf/u_btn_sclose.png'}),
					$CE.node('div', {className:'clr'})
				])
			]),
			$CE.node('div', {id:'FM_folderlist'}, $CE.node('div', {className:'sfix'}, '&nbsp;'))
		]));
		ph.appendChild($CE.node('div', {id:'FM_upload_progress'}));
		ph.appendChild($CE.node('div', {id:'FM_filelist'}, $CE.node('div', {className:'wait'})));
		ph.appendChild($CE.node('div', {className:'clr'}));
		$('FM_add_folder_f_btn').onclick = function(){ $('FM_add_folder_f').style.display='block'; };
		$('FM_add_folder_f_close').onclick = function(){ $('FM_add_folder_f').style.display='none'; };
		setTimeout(function(){ $('divStatus').innerHTML = $FM.count = 'Загружено 0'; }, 3000);
	},
	u_folders_s : function(){
		var r = new Ajax.Request('/cms/index.php', {
			method : 'post',
			parameters : { func : 'fm::folders_s' },
			on200 : this.w200_folders_s.bind(this),
			onFailure : function(){} //failure wrapper requiued
		});
	},
	w200_folders_s : function(req){
		var r = req.responseText.evalJSON(true), ph = $('FM_folderlist'), o = [];
		o.push($CE.node('option',{value:this.base},'/'));
		for (var i=0; i<r.length; i++){
			if (r[i]['v'] == this.path){
				o.push($CE.node('option',{value:r[i]['v'], selected:true},'/'+r[i]['v']));
			}else{
				o.push($CE.node('option',{value:r[i]['v']},'/'+r[i]['v']));
			}
		}
		var s = $CE.node('select',{name:'test',className:'folders'},o);
		s.onchange = function(){ $FM.s_root(this.value) };
		ph.replaceChild(s,ph.firstChild);
	},
	reinit : function(req){
		var r = req.responseText, ph = $('swfupload');
		if (r == 'error'){}
		else {
			swfu.destroy();
			ph.removeChild(ph.firstChild);
			ph.appendChild($CE.node('span', {id:'FM_browse_btn'}));			
			this.init(r);
		}
	},
	s_root : function(path){
		new Ajax.Request('/cms/index.php', {
				method: 'post',
				parameters: { func: 'fm::set_root', funca: path },
				on200: this.reinit.bind(this),
				onSucess: function(){}
			}
		);	
	},
	u_folders_list : function(){
		var r = new Ajax.Request('/cms/index.php', {
			method : 'post',
			parameters : { func : 'fm::flist' },
			on200 : this.w200_folders_list.bind(this),
			onFailure : function(){}
		});
	},
	w200_folders_list : function(req){
		//alert(req.responseText);
		var r = req.responseText.evalJSON(true), ph = $('FM_filelist'), e;
		ph.removeChild(ph.firstChild);
		for (var i=0; i<r.length; i++){
			if (r[i]['t'] == 'fld'){
				e = $CE.node('div', null,[$CE.node('div', {className:'folder'}), $CE.node('span', null, r[i]['n'])]);
				e.onclick = function(x){ return function(e){ $FM.s_root(x);}}(r[i]['d']);
				ph.appendChild(e);
			}else if (r[i]['t'] == 'fl'){
				e = $CE.node('div', null, [$CE.node('div', {className:'file'}), $CE.node('span', null, r[i]['n'])]);
				e.onclick = function(x){ return function(e){ $FM.returnv(x);}}(r[i]['d']);
				ph.appendChild(e); 
			}
		}
	},
	returnv : function(v){
		alert(this.returnf+'='+v);
	},
	swfDialogComplete : function(nSelected, nQueued){
		try{
			if(nSelected > 0){ $(swfu.customSettings.cancelButtonId).disabled = false; }
			swfu.startUpload();
			//alert($FM.total_size);
		}catch (ex){ swfu.debug(ex); }
	},
	swfUploadStart : function(file){
		try{
			$FM.progressCreate(file, swfu.customSettings.progressTarget, {status:"Загрузка...", cancelbtn:true});
		}catch (ex){}		
		return true;
	},
	swfUploadProgress : function(file, bLoaded, bTotal) {
		try{
			var p = Math.ceil((bLoaded / bTotal) * 100);	
			$FM.progressCreate(file, swfu.customSettings.progressTarget, {status:"Загрузка... "+p+"%", progress:p});
			$FM.total_loaded += bLoaded;
		} catch (ex) {
			swfu.debug(ex);
		}
	},
	swfFileQueued : function(file) {
		try {
			$FM.progressCreate(file, swfu.customSettings.progressTarget, {status:"Ожидает...", cancelbtn:true});
			$FM.total_size += file.size;
		} catch (ex) {
			swfu.debug(ex);
		}
	
	},
	swfQueueComplete : function(n){ $FM.count = 'Загружено ' + n;  },
	swfComplete : function(file){
		if (swfu.getStats().files_queued === 0) {
			$(swfu.customSettings.cancelButtonId).disabled = true;
			$FM.s_root($FM.path);
		}
	},
	swfUploadSuccess : function(file, serverData){
		try {
			$FM.progressCreate(file, swfu.customSettings.progressTarget, {status:"Загружено!", cancelbtn:false, complete:true});
		}catch (ex){ swfu.debug(ex); }
	},
	progressCreate : function(file, targetID, settings){
		//alert(file.name);
		var wrapper = document.getElementById(file.id);
		if (!wrapper){
			wrapper = $CE.node('div', {className:"progressWrapper", id:file.id})
			var e = $CE.node('div', {className:"progressContainer"}, [
					$CE.node('a', {className:"progressCancel", href:"#", 'style.visibility':"hidden"}, " "),
					$CE.node('div', {className:"progressName"}, file.name),//+file.size
					$CE.node('div', {className:"progressBarStatus"}, " "),
					$CE.node('div', {className:"progressBarInProgress"}, $CE.node('img', {src:'/cms/js/progress_fg.png'}))
				])
			wrapper.appendChild(e);
			$(targetID).appendChild(wrapper);
		}else{
			var e = wrapper.firstChild;
			e.className = "progressContainer";	
			e.childNodes[2].innerHTML = "&nbsp;";
			e.childNodes[2].className = "progressBarStatus";		
			e.childNodes[3].className = "progressBarInProgress";
			e.childNodes[3].childNodes[0].style.width = "0%";
		}
		if (settings){
			if (settings.status){ e.childNodes[2].innerHTML = settings.status; }
			if (settings.cancelbtn){
				e.childNodes[0].style.visibility = settings.cancelbtn ? "visible" : "hidden";
				if (swfu){ e.childNodes[0].onclick = function(){ swfu.cancelUpload(file.id); return false; }; }
			}
			if (settings.progress){
				e.className = "progressContainer green";
				e.childNodes[3].className = "progressBarInProgress";
				e.childNodes[3].childNodes[0].style.width = settings.progress + "%";
			}
			if (settings.complete && settings.complete == true){
				e.className = "progressContainer blue";
				e.childNodes[3].className = "progressBarComplete";
				e.childNodes[3].childNodes[0].style.width = "0";
			}
		}
		//alert('ok1');
	}
};
