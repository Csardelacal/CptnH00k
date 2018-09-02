
<div class="spacer" style="height: 30px"></div>

<div class="row l6">
	<div class="span l4">
		<h2 class="unpadded">Stats</h2>
		
		<div class="graph" style="border-bottom: solid 1px #CCC;"><!--
			<?php $count = count($stats); ?>
			<?php foreach ($stats as $stat): ?>
			--><div class="column" style="line-height: 300px; display: inline-block; height: 300px; width: <?= 100 / $count ?>%; position: relative; vertical-align: bottom;">
				<div class="series" style="height: <?= $stat['in'] / $max * 300 ?>px; bottom: 0; width: 100%; position: absolute; background: rgba(30, 130, 210, 0.3)"></div>
				<div class="series" style="height: <?= $stat['out'] / $max * 300 ?>px; bottom: <?= $stat['in'] / $max * 300 ?>px; width: 100%; position: absolute; background: rgba(220, 30, 30, 0.3)"></div>
			</div><!--
			<?php endforeach; ?>
		--></div>
	</div>
	<div class="span l2">
		<div class="material unpadded">
			<div class="padded">
				<h2 class="unpadded">Apps</h2>
			</div>
			<ul class="list">
				<?php foreach ($apps as $app): ?>
				<li><a href="<?= url('app', 'hooks', $app->_id) ?>"><?= $app->name ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
