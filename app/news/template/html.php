<!DOCTYPE html>
<html lang="id">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>News <?= $city ?></title>
	<body>
		<ul>
		<?php foreach ($rss->item as $item): ?>
			<li>
				<?php if (isset($item->{'content:encoded'})): ?>	
					<h2>
						<a href="<?= htmlSpecialChars($item->link) ?>" target="_blank">
							<?= htmlSpecialChars($item->title) ?>
						</a>
						<small><?= date("j F, Y", (int) $item->timestamp) ?></small>
					</h2>
					<div><?= $item->{'content:encoded'} ?></div>
				<?php else: ?>
					<div><?= html_entity_decode($item->description) ?></div>
				<?php endif ?>
			</li>
		<?php endforeach ?>
		</li>
	</body>
</html>