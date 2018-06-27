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

class OutboxModel extends Model
{
	
	/**
	 * 
	 * @param Schema $schema
	 */
	public function definitions(Schema $schema) {
		$schema->app       = new Reference('authapp');
		$schema->url       = new StringField(4096);
		$schema->payload   = new TextField();
		$schema->attempt   = new IntegerField(true);
		
		$schema->scheduled = new IntegerField(true);
		$schema->delivered = new IntegerField(true);
		$schema->created   = new IntegerField(true);
		
		/*
		 * The response the system received from the remote server, this should
		 * help debugging and analizing issues when there is connection issues.
		 * 
		 * This property should contain a string, containing the response
		 * code, headers and body of the response.
		 * 
		 * It is not a property intended to be read by a machine, but by a human
		 * trying to find issues with the infrastructure or the target application,
		 * therefore there is no guaranteed format.
		 */
		$schema->response  = new TextField();
	}
	
	public function onbeforesave() {
		if (!$this->created) {
			$this->created = time();
		}
		
		if (!$this->attempt) {
			$this->attempt = 1;
		}
	}

}
