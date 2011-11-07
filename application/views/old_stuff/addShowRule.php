<?=form_open("xem/editShowRuleProcess")?>
<ul>
	<li>
		<label for="element_id">Show:</label>
		<select name="element_id" <?=$disabeld?>>
			<? foreach($shows->result() as $row):?>
			<option value="<?=$row->id?>"><?=$row->main_name?></option>
			<? endforeach?>
		</select>
	</li>
	<li>
		<label for="origin_id">Origin:</label>
		<select name="origin_id[]" <?=$disabeld?> size="3" multiple>
			<? foreach($locations->result() as $row):?>
			<option value="<?=$row->id?>"<?if($row->name=="tvdb")echo "selected";?>><?=$row->name?></option>
			<? endforeach?>
		</select>
	</li>
	<li>
		<label for="destination_id[]">Destination:</label>
		<select name="destination_id" <?=$disabeld?> size="3" multiple>
			<? foreach($locations->result() as $row):?>
			<option value="<?=$row->id?>" <?if($row->name=="scene")echo "selected";?>><?=$row->name?></option>
			<? endforeach?>
		</select>
	</li>
	<li>
		<label for="season_from">Season from</label>
		<input name="season_from" <?=$disabeld?> /> "start" for first season
	</li>
	<li>
		<label for="season_to">Season to</label>
		<input name="season_to" <?=$disabeld?> /> "end" for latest season
	</li>
	<li>
		<label for="season_offset">Season Offset</label>
		<input name="season_offset" <?=$disabeld?> />
	</li>
	<li>
		<label for="episode_from">Episdode from</label>
		<input name="episode_from" <?=$disabeld?> /> "start" for first episode
	</li>
	<li>
		<label for="episode_to">Episdode to</label>
		<input name="episode_to" <?=$disabeld?> /> "end" for latest episode
	</li>
	<li>
		<label for="episode_offset">Episdode Offset</label>
		<input name="episode_offset" <?=$disabeld?> />
	</li>
	<li>
		<label for="absolute_ep_offset">Absolute Ep Offset</label>
		<input name="absolute_ep_offset" <?=$disabeld?> />
	</li>
	<li>
		<input type="submit" value="add rule" <?=$disabeld?>/>
	</li>
</ul>
</form>