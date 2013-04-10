<ul>
<?php if (!empty($this->elements)): ?>
<?php $this->i=0; ?>
<?php foreach ($this->elements as $element): ?>
	<?php if (!empty($element['childs'])): ?>
	<li onmouseover="this.className='hover'" onmouseout="this.className='nohover'">
	<?php else: ?>
	<li>
	<?php endif; ?>
		<?php $link = ($element['link'] != '') ? ' href="' . $element['link'] . '"' : ''; ?>
		<?php $current = ($element['current'] == 1) ? ' class="active"' : ''; ?>
		<a<?php echo $link; ?><?php echo $element['browser_nav']; ?><?php echo $current; ?>>
			<?php echo $element['title']; ?>
		</a>
		<?php echo $element['childs']; ?>
	</li>
	<?php if ($this->i < count($this->elements)-1): ?>
	<li class="menu_separator">&nbsp;</li>
	<?php endif; ?>
	<?php $this->i++; ?>
<?php endforeach; ?>
<?php endif; ?>
</ul>
