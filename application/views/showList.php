<ul>
	<?foreach($curShows as $show):?>
	<li>
		<?=anchor('xem/show/'.$show->id,$show->main_name)?>
		<?if(isset($show->name)):?>
		(<?=$show->name?>)
		<?endif?>
	</li>
	<?endforeach?>
</ul>