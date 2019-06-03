
<div class="spacer" style="height: 30px"></div>

<div class="row l1">
	<div class="span l1">
		<div class="navigation tabs">
			<a class="navigation-item <?= $role == 'from'? '' : 'active' ?>" href="<?= url('listener', 'registered', 'to:' . $selectedApp) ?>">Incoming</a>
			<a class="navigation-item <?= $role == 'from'? 'active' : '' ?>" href="<?= url('listener', 'registered', 'from:' . $selectedApp) ?>">Outgoing</a>
		</div>
	</div>
</div>

<div class="spacer" style="height: 30px"></div>

<div class="row l1">
	<div class="span l1">
		
		<?php foreach ($listeners as $hook): ?>
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
				<div><a href="<?= url('listener', 'edit', $hook->_id) ?>">Edit</a></div>
			</div>
		</div>
		
		<div class="separator"></div>
		<?php endforeach; ?>
	</div>
</div>
