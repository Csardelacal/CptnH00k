<!DOCTYPE html>
<html>
	<head>
		<title><?= isset(${'page.title'})? ${'page.title'} : 'CptnH00k - Webhook server' ?></title>
		<meta name="_scss" content="<?= \spitfire\SpitFire::baseUrl() ?>/assets/scss/_/js/">
		<link rel="stylesheet" type="text/css" href="<?= \spitfire\core\http\URL::asset('css/app.css') ?>">
	</head>
	<body>
		
		<div class="navbar">
			<div class="left">
				<span class="toggle-button dark"></span>
				<a href="<?= url() ?>">H00k</a>
			</div>
			<div class="right">
				<a href="<?= url('user', 'logout') ?>">Logout</a>
			</div>
		</div>
		
		<div><!--
			--><div class="contains-sidebar">
				<div class="sidebar">
					<div class="spacer" style="height: 10px"></div>
					<a class="menu-entry" href="<?= url() ?>">Dashboard</a>
					
					<span class="menu-title">Apps</span>
					
					<?php foreach ($apps as $app): ?>
					<a class="menu-entry <?= $selectedApp == $app->appID? 'active' : '' ?>" href="<?= url('listener', 'registered', 'to:' . $app->appID) ?>"><?= $app->name ?></a>
					<?php endforeach; ?>
				</div>
			</div><!--
			--><div class="content">
				<?= $this->content() ?>
			</div><!--
		--></div>
		
		<script type="text/javascript" src="<?= \spitfire\core\http\URL::asset('js/m3/depend.js') ?>"></script>
		<script type="text/javascript" src="<?= \spitfire\core\http\URL::asset('js/m3/depend/router.js') ?>"></script>
		
		<script type="text/javascript">
			depend(['m3/depend/router'], function (router) {
				router.all().to(function (str) { return '<?= \spitfire\core\http\URL::asset('') ?>js/' + str + '.js'; });
				router.equals('_scss').to( function() { return '<?= \spitfire\SpitFire::baseUrl() ?>/assets/scss/_/js/_.scss.js'; });
			});
			
			depend(['_scss'], function() {
				//Loaded
			});
		</script>
	</body>
</html>