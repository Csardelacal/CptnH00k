
<form method="POST" action="">
	<?php $apps = db()->table('authapp')->getAll()->all(); ?>
	<label>Source:</label> 
	<select name="app">
		<option value="">---</option>
		<?php foreach ($apps as $app): ?>
		<option value="<?= $app->appID ?>"><?= $app->name ?></option>
		<?php endforeach; ?>
	</select>
	<label>Target:</label> 
	<select name="target">
		<option value="">---</option>
		<?php foreach ($apps as $app): ?>
		<option value="<?= $app->appID ?>"><?= $app->name ?></option>
		<?php endforeach; ?>
	</select>
	
	
	<label>Internal ID (for reference, must be unique for the target app):</label> 
	<input type="text" name="hid">
	
	
	<label>Listen for:</label> 
	<input type="text" name="listen">
	
	
	<label>URL:</label> 
	<input type="text" name="url">
	
	
	<label>Delay:</label> 
	<input type="text" name="defer" value="0">
	
	
	<label>Format:</label>  
	<select name="format">
		<option value="json">JSON</option>
		<option value="xml">XML</option>
		<option value="nvp">Key-Value Pairs</option>
	</select>
	
	<input type="submit">
</form>