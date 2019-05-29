
<form method="POST" action="">
	<?php $apps = db()->table('authapp')->getAll()->all(); ?>
	
	<div class="spacer" style="height : 20px"></div>
	
	<div class="row l1">
		<div class="span l1">
			<h2>Key settings</h2>
		</div>
	</div>
	
	<div class="spacer" style="height : 20px"></div>
	
	<div class="row l6 m3">
		<div class="span l1 m1">
			<label>Source</label> 
		</div>
		<div class="span l2 m2">
			<select class="styled-select" name="app" style="width: 100%">
				<option value="">---</option>
				<?php foreach ($apps as $app): ?>
				<option value="<?= $app->appID ?>"><?= $app->name ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="span l1 m1">
			<label>Target</label> 
		</div>
		<div class="span l2 m2">
			<select  class="styled-select" name="target" style="width: 100%">
				<option value="">---</option>
				<?php foreach ($apps as $app): ?>
				<option value="<?= $app->appID ?>"><?= $app->name ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	
	<div class="spacer" style="height : 15px"></div>
	
	<div class="row l6">
		<div class="span l1">
			<label>Internal ID</label> 
		</div>
		<div class="span l2">
			<input type="text" name="hid" style="width: 100%">
			<p class="small secondary">
				Used for reference, must be unique for the target app. When creating 
				two hooks with the same target and ID combination, the first one will
				be overwritten.
			</p>
		</div>
		<div class="span l1">
			<label>Listen for</label> 
		</div>
		<div class="span l2">
			<input type="text" name="listen" style="width: 100%">
		</div>
	</div>
	
	<div class="spacer" style="height : 15px"></div>
	
	<div class="row l6">
		<div class="span l1">
			<label>URL:</label> 
		</div>
		<div class="span l5">
			<input type="text" name="url" style="width: 100%">
		</div>
	</div>
	
	<div class="spacer" style="height : 20px"></div>
	
	<div class="row l1">
		<div class="span l1">
			<h2>Additional settings</h2>
		</div>
	</div>
	
	
	
	
	<div class="spacer" style="height : 15px"></div>
	
	<div class="row l6">
		<div class="span l1">
			<label>Transliterate:</label> 
		</div>
		<div class="span l5">
			<textarea name="transliteration" style="width: 100%"></textarea>
			<p class="small secondary">
				Transliteration allows the data the source application sends to be 
				converted to a different format, allowing to connect potentially
				incompatible applications with each other.
			</p>
		</div>
	</div>
	
	<div class="spacer" style="height : 15px"></div>
	
	<div class="row l6">
		<div class="span l1">
			<label>Delay</label> 
		</div>
		<div class="span l2">
			<input type="text" name="defer" value="0" style="width: 100%; text-align: right">
		</div>
		<div class="span l1">
			<label>Format</label>  
		</div>
		<div class="span l2">
			<select name="format" class="styled-select" style="width: 100%">
				<option value="json">JSON</option>
				<option value="xml">XML</option>
				<option value="nvp">Key-Value Pairs</option>
			</select>
		</div>
	</div>
	
	<div class="spacer" style="height : 15px"></div>
	
	<div class="row l1">
		<div class="span l1" style="text-align: right">
			<input type="submit">
		</div>
	</div>
</form>