<!DOCTYPE html>
<html>
	<head>
		<title><?= isset(${'page.title'})? ${'page.title'} : 'CptnH00k - Webhook server' ?></title>
		<link rel="stylesheet" type="text/css" href="<?= \spitfire\core\http\URL::asset('css/app.css') ?>">
	</head>
	<body>
		
		<div class="navbar">
			<div class="left">
				<a href="<?= url() ?>">H00k</a>
			</div>
			<div class="right">
				<a href="<?= url('user', 'logout') ?>">Logout</a>
			</div>
		</div>
		<?= $this->content() ?>
	</body>
</html>