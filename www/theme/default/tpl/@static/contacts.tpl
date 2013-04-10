<div class="center_mod_title"><?php echo $this->item['title']; ?></div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<div class="col_center_padding">
        <?php echo $this->item['fulltext']; ?>
    </div>
</div>
<div class="center_mod_title">Обратная связь</div>
<div class="center_mod_body">
    <form name="callback_form" action="#" method="post" enctype="multipart/form-data" onsubmit="return callback(this,event);">
    <input type="hidden" name="function" value="typedcontent->callback" />
    <table class="form" cellpadding="0" cellspacing="0" style="margin-top:10px;">
        <tr>
            <td class="col1">Заголовок</td>
            <td class="col2" colspan="2"><input type="text" name="title" /></td>
        </tr>
        <tr>
            <td class="col1">Эл. почта</td>
            <td class="col2" colspan="2"><input type="text" name="email" /></td>
        </tr>
        <tr>
            <td colspan="3">Текст сообщения</td>
        </tr>
        <tr>
            <td colspan="3"><textarea name="message" style="height:200px;"></textarea></td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
            <td class="col3"><input class="submit" style="width:110px;" type="submit" value="Отправить" /></td>
        </tr>
        <tr>
            <td colspan="3"><div class="stat" id="callback_stat"></div></td>
        </tr>
    </table>
    </form>
</div>
<script>
    function callback(data, event)
    {
        event.stop();
        if (data.tagName.toUpperCase() == 'FORM'){
            var data = data.serialize(true);
        }    
        
        new Ajax.Request('/index.php', {
            method: 'post',
            parameters: data,
            encoding: 'UTF-8',
            onCreate : (function(){
                document.callback_form.title.setStyle({outline:"none"});
                document.callback_form.email.setStyle({outline:"none"});
                document.callback_form.message.setStyle({outline:"none"});
                document.callback_form.disable();
            }).bind(this),
            onComplete : (function(request){
                eval(request.responseText);
            }).bind(this)
        });
        return false;
    }
</script>
