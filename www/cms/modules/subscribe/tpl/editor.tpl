<div class="editor_wrapper">
	<form name="{#name#}" method="post" action="#">
	<input type="hidden" name="db_id" value="{#id#}">
	<table class="editor" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td>
				<table class="e_left" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="1%">���������</td>
						<td><select name="category" class="text">{#tree#}</select></td>
					</tr>
					<tr>
						<td class="a">���������</td>
						<td class="b"><input type="text" class="text" name="title" value="{#title#}" /></td>
					</tr>
					<tr>
						<td nowrap="nowrap">���������</td>
						<td class="b"><input type="text" class="text" name="title_alias" value="{#alias#}" /></td>
					</tr>
					<tr>
						<td nowrap="nowrap">������������</td>
						<td><input type="checkbox" name="published" {#published#} /></td>
					</tr>
					<tr>
						<td colspan="2"><br />������� �������� [�������������]</td>
					</tr>
					<tr>
						<td colspan="2"><textarea class="text" name="introtext">{#introtext#}</textarea></td>
					</tr>
					<tr>
						<td colspan="2"><br />������ �������� [�������������]</td>
					</tr>
					<tr>
						<td colspan="2"><textarea class="text" name="fulltext">{#fulltext#}</textarea></td>
					</tr>
				</table>
			</td>
			<td width="400" style="padding:10px;">
				<div class="tabs_btn" id="tabs_btn">
					<a href="javascript:tabs('tabs', 0)" class="selected"><span>���������</span></a>
					<a href="javascript:tabs('tabs', 1)"><span>��������</span></a>
					<a href="javascript:tabs('tabs', 2)"><span>�������������</span></a>
					<div class="clr"></div>
				</div>
				<div id="tabs">
					<div class="tab" style="display:block;">
						<table class="advanced" cellspacing="0" cellpadding="5">
							<tr>
								<td class="a1" nowrap="nowrap">�����</td>
								<td class="a2" colspan="2">
									<select name="created_by" class="text">
										{#created_by#}
									</select>
								</td>
							</tr>
							<tr>
								<td class="a1" nowrap="nowrap">���� ��������</td>
								<td class="a2"><input type="text" class="text" name="created" value="{#created#}" /></td>
								<td class="a3"><input type="button" class="w_50" value=" ... " onclick="call('core', 'calendar', '{#name#}.created')" /></td>
							</tr>
							<tr>
								<td class="a1" nowrap="nowrap">������������ �</td>
								<td class="a2"><input type="text" class="text" name="publish_up" value="{#publish_up#}" /></td>
								<td class="a3"><input type="button" class="w_50" value=" ... " /></td>
							</tr>
							<tr>
								<td class="a1" nowrap="nowrap">������������ ��</td>
								<td class="a2"><input type="text" class="text" name="publish_down" value="{#publish_down#}" /></td>
								<td class="a3"><input type="button" class="w_50" value=" ... " /></td>
							</tr>
							<tr>
								<td class="a1">����</td>
								<td class="a2">{#hits#}</td>
								<td class="a3"><input type="button" class="w_50" value="�����" /></td>
							</tr>
						</table>
					</div>
					<div class="tab">
						{#image_editor#}
					</div>
					<div class="tab">
						<table class="advanced" cellspacing="0" cellpadding="5">
							{#adv_right#}
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
	</form>
</div>
<div class="clr"></div>
