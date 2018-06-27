<?php

use spitfire\Model;
use spitfire\storage\database\Schema;


class PSKModel extends Model
{
	
	/**
	 * 
	 * @param Schema $schema
	 */
	public function definitions(Schema $schema) {
		$schema->app     = new Reference('authapp');
		$schema->PSK     = new StringField(200);
		$schema->expires = new IntegerField(true);
	}

}