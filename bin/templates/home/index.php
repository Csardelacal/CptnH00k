
<div class="spacer" style="height: 30px"></div>

<div class="row l3">
	<div class="span l2">
		<div class="material unpadded">

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
		
		<div class="spacer" style="height: 30px;"></div>
		
		<div class="row l1">
			<div class="span l1">
				<h2 class="unpadded">Recent outgoing</h2>
			</div>
		</div>
		
		<div class="spacer" style="height: 15px;"></div>
		
		<?php foreach(db()->table('outbox')->getAll()->setOrder('created', 'DESC')->range(0, 10) as $sample): ?>
		<div class="row l6">
			<div class="span l4">
				<div  style="font-size: .8em">
					to <strong><?= $sample->app->name ?></strong> (<?= $sample->delivered? 'Sent' : 'Queued' ?> - <span title="Attempt number - High attempt numbers indicate an issue at the receiving end">#<?= $sample->attempt ?></span>)
				</div>
			</div>
			<div class="span l2" style="text-align: right">
				<div  style="font-size: .7em">
					<?= Time::relative($sample->created) ?>
				</div>
			</div>
		</div>
		<div class="spacer" style="height: 10px"></div>
		<?php endforeach; ?>
	</div>
	<div class="span l1">
		<div class="row l1">
			<div class="span l1">
				<h2 class="unpadded">Recent incoming</h2>
			</div>
		</div>
		
		<div class="spacer" style="height: 15px;"></div>
		
		<?php foreach(db()->table('inbox')->getAll()->setOrder('created', 'DESC')->range(0, 25) as $sample): ?>
		<div class="row l6">
			<div class="span l4">
				<div  style="font-size: .8em">
					from <strong><?= $sample->app->name ?></strong> (<?= $sample->trigger ?>)
				</div>
			</div>
			<div class="span l2" style="text-align: right">
				<div  style="font-size: .7em">
					<?= Time::relative($sample->created) ?>
				</div>
			</div>
		</div>
		<div class="spacer" style="height: 10px"></div>
		<?php endforeach; ?>
	</div>
</div>
