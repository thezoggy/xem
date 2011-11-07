<?=form_open("xem/addShowProcess")?>
<ul>

	<li>
		<label for="location_id">Location:</label>
		<select name="location_id" <?=$disabeld?>>
			<? foreach($locations->result() as $row):?>
			<?if($row->name == "scene"){continue;}?>
			<option value="<?=$row->id?>"><?=$row->name?></option>
			<? endforeach?>
		</select>
	</li>
	<li>
		<label for="id">id</label>
		<input name="id" <?=$disabeld?> />
	</li>
	<li>
		<label for="main_name">main name</label>
		<input name="main_name" <?=$disabeld?> />
	</li>
	<li>
		<input type="submit" value="add show" <?=$disabeld?>/>
	</li>
</ul>
</form>