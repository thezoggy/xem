<!DOCTYPE html>
<html lang="en">
<head></head>
<body>
<h1><?=$user_nick?> created the show <?=$show->main_name?></h1>

<p>
and it can be found here &rarr; <?=anchor('http://thexem.info/xem/show/'.$show->id, $show->main_name)?>
</p>

<hr/>
<p style="font-size: 90%;">
To deactivate these email notifications go to <?=anchor('http://thexem.info/user/', 'User Config')?>
</p>
</body>
</html>