<div class="right_mod_title">����������� �����</div>
<div class="right_mog_body">
	<div class="left_menu">
		<ul>
            <?php if (!empty($this->club)): ?>
	        <li><a href="/club/club<?php echo $this->club['id']; ?>">����</a></li>
	        <li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/afishi">�����</a></li>
			<li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/photos">����</a></li>
			<li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/videos">�����</a></li>
			<li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/users">���������� � ���������� � �������</a></li>
			<li class="menu_separator">&nbsp;</li>
			<li><a href="/club/club<?php echo $this->club['id']; ?>/news">������� �����</a></li>
            <?php else: ?>
	        <li><a href="/club/add">������� ����</a></li>
            <?php endif; ?>
			<li class="menu_separator">&nbsp;</li>
            <li><a href="/club/admin">������������� ������ ������</a></li>
			<li class="exit"><input class="submit" type="submit" value="�����" onClick="logout();" /></li>
		</ul>
	</div>
</div>
<script>
	function logout()
	{
		new Ajax.Request('/index.php', {
			method: 'post',
			parameters: {'function':'club_admin->adminLogout','location':window.location.href},
			encoding: 'UTF-8',
			onCreate : (function(){}).bind(this),
			onComplete : (function(request){
				eval(request.responseText);
			}).bind(this)
		});		
	}
</script>
