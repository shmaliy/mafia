<div class="right_mod_title">������� ������</div>
<div class="right_mog_body">
	<?php if (!empty($this->newsContList)): ?>
	<?php foreach ($this->newsContList as $item): ?>
    	<div class="fp_news_i">
    		<!--div class="fp_news_i_club">����</div-->
    		<div class="fp_news_i_title"><a href="/club/club<?php echo $item['clubId']; ?>/news/news<?php echo $item['id']; ?>"><?php echo $item['title']; ?></a></div>
    	</div>
	<?php endforeach; ?>
	<?php else: ?>
	<div class="empty" style="padding-left:15px;">��� ��������</div>
	<?php endif; ?>
</div>
