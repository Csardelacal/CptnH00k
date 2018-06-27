<?php

use spitfire\mvc\Director;

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

class InboxDirector extends Director
{
	
	public function process() {
		$pending = db()->table('inbox')->get('processed', null)->where('scheduled', '<', time())->range(0, 20);
		
		foreach ($pending as $record) {
			$pieces = explode('.', $record->trigger);
			
			$query = db()->table('listener')->get('source', $record->app);
			$group = $query->group();
			$group->where('listenTo', $record->trigger);
			
			array_pop($pieces);
			
			while (!empty($pieces)) {
				$group->where('listenTo', implode('.', $pieces) . '*');
				array_pop($pieces);
			} 
			
			$listeners = $query->all();
			
			foreach ($listeners as $listener) {
				$outbox = db()->table('outbox')->newRecord();
				$outbox->app = $listener->target;
				
				//TODO: Authenticate PSK apps too
				$outbox->url = $listener->URL;
				$outbox->payload = $record->payload; //TODO: Perform transliteration here
				$outbox->scheduled = $record->scheduled + $listener->defer;
				$outbox->store();
			}
			
			$record->processed = time();
			$record->store();
		}
	}
	
}