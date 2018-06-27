<?php

use spitfire\Model;
use spitfire\storage\database\Schema;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ListenerModel extends Model
{
	
	/**
	 * 
	 * @param Schema $schema
	 */
	public function definitions(Schema $schema) {
		/*
		 * Depending on whether the application we're using is using PHPAS or not,
		 * the system needs a different authentication mechanism. If the other 
		 * software is not using PHPAS, CptnH00k will provide the system with a 
		 * pre-shared key, that is generated when creating the hook.
		 * 
		 * Otherwise, a proper signature will be created, allowing the 
		 * other app to authenticate CptnH00k against a PHPAS instance.
		 */
		$schema->source     = new Reference('authapp');
		$schema->target     = new Reference('authapp');
		
		$schema->internalId = new StringField( 50);
		$schema->listenTo   = new StringField( 50);
		$schema->URL        = new StringField(200);
		$schema->defer      = new IntegerField(true);
		
		$schema->transliteration = new TextField();
		$schema->format          = new EnumField('json', 'xml', 'nvp');
		
		$schema->created    = new IntegerField(true);
		$schema->createdBy  = new StringField(50);
	}
	
	public function onbeforesave() {
		if (!$this->created) { $this->created = time(); }
	}

}