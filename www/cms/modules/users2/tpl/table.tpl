	<div class="table_filter">
		<div class="filter_left">��� <select id="category" onchange="call('{#name#}', '_set_parent', this.value)">{#tree#}</select></div>
	</div>
	<table class="ctable" cellspacing="1">
		<thead>
			<tr>
				<th>ID</th>
				<th>&nbsp;</th>
				<th class="title">��� / �����</th>
				<th>�� �����</th>
				<th>��������</th>
				<th>������</th>
				<th>����������� �����</th>
				<th>���� �����������</th>
				<th>��������� �����</th>
				{#adv_fields#}
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="30"><span id="navigator">{#navigator#}</span></th>
			</tr>
		</tfoot>
		<tbody id="ctable_contents">
			{#items#}
		</tbody>
	</table>