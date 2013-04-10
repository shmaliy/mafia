<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_admin = {' . implode(',', $this->constants) . '}';
}
?></script>
<div class="center_mod_title">Восстановление пароля</div>
<?php if ($this->centerBanner): ?>
<div class="banner_center">
<?php echo $this->centerBanner['introtext']; ?>
</div>
<?php endif; ?>
<div class="center_mod_body">
	<form name="password_repair_form" action="#" method="post" enctype="multipart/form-data" onsubmit="return password_repair(this,event);">
    <input type="hidden" name="function" value="club_admin->adminRepairPass" />
	<table class="form" cellpadding="0" cellspacing="0">
		<tr>
			<td class="col1">Эл. почта</td>
			<td class="col2" colspan="2"><input type="text" name="email" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="col3" colspan="2"><input class="submit" type="submit" value="Отправить" /></td>
		</tr>
		<tr>
			<td colspan="3"><div class="stat" id="club_admin_stat"></div></td>
		</tr>
	</table>
    </form>
</div>
<script>
	function password_repair(data, event)
	{
		event.stop();
		if (data.tagName.toUpperCase() == 'FORM'){
			var data = data.serialize(true);
		}
		data.location = window.location.href;
		
		new Ajax.Request('/index.php', {
			method: 'post',
			parameters: data,
			encoding: 'UTF-8',
			onCreate : (function(){
				document.password_repair_form.email.setStyle({outline:"none"});
			}).bind(this),
			onComplete : (function(request){
				eval(request.responseText);
			}).bind(this)
		});
		return false;		
	}
</script>
