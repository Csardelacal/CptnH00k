
<div class="spacer" style="height: 30px"></div>

<div class="row l6">
	<div class="span l4">
		
		<?php foreach ($hooks as $hook): ?>
		<div class="row l4">
			<div class="span l3">
				<div>
					<a href="<?= url('app', 'hooks', $hook->source->_id) ?>"><?= $hook->source->name ?></a> :: <?= $hook->listenTo ?>
				</div>
				<div>
					ID: <?= $hook->internalId ?>
				</div>
				<div>
					URL: <?= $hook->URL ?>
				</div>
			</div>
			
			<div class="span l1">
				<div><a href="<?= url('listener', 'drop', $hook->_id) ?>">Delete</a></div>
				<div><a href="<?= url() ?>">Edit</a></div>
			</div>
		</div>
		
		<div class="separator"></div>
		<?php endforeach; ?>
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
