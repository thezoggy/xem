<!DOCTYPE html>
<html lang="en">
<head></head>
<body>
<h1><?=$user_nick?> created the show <?=$show->main_name?></h1>

<p>
and it can be found here &rarr; <?=anchor('http://thexem.de/xem/show/'.$show->id, $show->main_name)?>
</p>

</body>
</html>