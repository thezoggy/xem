
<?if($curShows):?>
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
<?else:?>
<h1>No Shows Found</h1>
<?endif;?>