<div class="editor_wrapper">
	<form name="{#name#}" method="post" action="#">
	<table class="editor" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td>
				<table class="e_left" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="1%" class="a" nowrap="nowrap">��������� ���������</td>
						<td class="b"><input type="text" class="text" name="title" /></td>
					</tr>
					<tr>
						<td colspan="2">����� ���������</td>
					</tr>
					<tr>
						<td colspan="2"><textarea class="text" name="text"></textarea></td>
					</tr>
				</table>
			</td>
			<td width="400" style="padding:10px;">
				<div class="tabs_btn" id="tabs_btn">
					<a class="selected"><span>������ �������</span></a>
					<div class="clr"></div>
				</div>
				<div id="tabs">
					<div class="tab" style="display:block; padding:5px;">
						<div style="overflow:auto; height:230px;">
							<table cellspacing="0" cellpadding="2">
								<tr>
									<td nowrap="nowrap">�����</td>
									<td width="1%">������</td>
								</tr>
								{#items#}
							</table>
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>
	</form>
</div>
<div class="clr"></div>
