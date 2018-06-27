<?php

use spitfire\Model;
use spitfire\storage\database\Schema;

/* 
 * The MIT License
 *
 * Copyright 2018 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class AuthAppModel extends Model
{
	
	/**
	 * 
	 * @param Schema $schema
	 */
	public function definitions(Schema $schema) {
		/**
		 * Applications that do not support PHPAS authentication will have an app ID
		 * prefied with "hook:" to indicate that they do not support external 
		 * authentication. PHPAS will provide numeric ID only.
		 */
		$schema->appID         = new StringField(50);
		$schema->name          = new StringField(50);
		$schema->sso           = new Reference('authapp');
		
		/**
		 * CptnH00k can provide SSO authentication for several simultaneous PHPAS instances,
		 * since more often than not, cptnh00k instances are more versatile than
		 * SSO servers. Hooks, usually carry little information and can be deleted
		 * shortly after they were executed.
		 * 
		 * As opposed to this, a PHPAS instance must contain data for just one 
		 * "organization". To prevent the need for idling hook dispatchers, we allow
		 * one dispatcher to service several applications.
		 * 
		 * Please note, that applications cannot send hooks across authentication 
		 * servers.
		 */
		$schema->isSSO         = new BooleanField();
		$schema->ssoURL        = new StringField(400);
		
		/*
		 * The configuration should be uploaded by the application as a single JSON
		 * string, allowing CptnH00k to understand the hooks and configuration 
		 * available from this application.
		 */
		$schema->configuration = new TextField();
	}
	
	public function noSSOSupport() {
		return Strings::startsWith($this->appId, 'hook:');
	}
	
	public function getPSK() {
		
		$db = $this->getTable()->getDb();
		$query = $db->table('psk')->get('app', $this)->group()->where('expires', '<', time())->where('expires', null)->endGroup();
		
		$record = $query->first();
		
		if (!$record) {
			$record = db()->table('psk')->newRecord();
			$record->app = $this;
			$record->expires = $this->sso || $this->isSSO? time() + 86400 * 20 : null;
			$record->PSK = str_replace(['?', '-', '='], '', base64_encode(random_bytes(100)));
			$record->store();
		}
		
		return $record->PSK;
		
	}

}