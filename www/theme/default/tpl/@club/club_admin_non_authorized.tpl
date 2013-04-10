<script><?php 
if (!empty($this->constants)) {
    foreach ($this->constants as $const => &$value) {
        $value = $const . ": '" . $value . "'";
    }
    echo 'var club_admin = {' . implode(',', $this->constants) . '}';
}
?></script>
<div class="right_mod_title">Администратор клуба</div>
<div class="right_mog_body">
	<form name="auth" action="#" onsubmit="return authorize(this);">
	<input type="hidden" name="function" value="club_admin->adminAuthorization">
	<table class="auth" cellpadding="0" cellspacing="0">
		<tr>
			<td width="60">Эл. почта</td>
			<td width="95"><input type="text" name="email" /></td>
		</tr>
		<tr>
			<td>Пароль</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input class="submit" type="submit" value="Войти" /></td>
		</tr>
		<tr>
			<td colspan="2"><div class="stat" id="auth_stat"></div></td>
		</tr>
	</table>
	</form>
	<div class="auth_link"><a href="/club/passrepair">Забыли пароль?</a></div>
	<div class="auth_link"><a href="/club/register">Регистрация</a></div>
</div>
<script>
	function authorize(data)
	{
		if (data.tagName.toUpperCase() == 'FORM'){
			var data = data.serialize(true);
		}
		data.location = window.location.href;
		
		new Ajax.Request('/index.php', {
			method: 'post',
			parameters: data,
			encoding: 'UTF-8',
			onCreate : (function(){
				document.auth.email.setStyle({outline:"none"});
				document.auth.password.setStyle({outline:"none"});
			}).bind(this),
			onComplete : (function(request){
				eval(request.responseText);
			}).bind(this)
		});
		return false;
	}
</script>
