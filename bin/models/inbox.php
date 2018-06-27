<?php

use spitfire\Model;

class InboxModel extends Model
{
	
	public function definitions(\spitfire\storage\database\Schema $schema) {
		$schema->app       = new Reference('authapp');
		$schema->trigger   = new StringField(100);
		$schema->payload   = new TextField();
		
		$schema->scheduled = new IntegerField(true);
		$schema->processed = new IntegerField(true);
		$schema->created   = new IntegerField(true);
	}
	
	public function onbeforesave() {
		if (!$this->created) {
			$this->created = time();
		}
	}

}