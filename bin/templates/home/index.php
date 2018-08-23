
<div class="spacer" style="height: 30px"></div>

<div class="row l6">
	<div class="span l4">
		<h2>Stats</h2>
	</div>
	<div class="span l2">
		<h2>Apps</h2>
		<ul>
			<?php foreach ($apps as $app): ?>
			<li><?= $app->name ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
