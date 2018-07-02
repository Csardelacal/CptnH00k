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
			
			/*
			 * The system triggers the event and all "parent" events, so if an app
			 * triggers "user.updated.1337", the system will notify applications
			 * listening to "user.updated.1337", "user.updated.*", "user.*" and "*"
			 * 
			 * Please note, that it will not trigger events without a wildcard, so,
			 * applications listening for "user.updated" will not be notified when
			 * "user.updated.1337" is called.
			 */
			$group = $query->group();
			$group->where('listenTo', $record->trigger);
			
			do {
				array_pop($pieces);
				$group->where('listenTo', implode('.', $pieces) . '.*');
			} 
			while (!empty($pieces));
			
			$listeners = $query->all();
			
			/*
			 * Loop over all the listeners that were registered for this application.
			 * 
			 * This block is the reason that we defer processing the inbox instead
			 * of immediately processing them when received. There may be dozens, or
			 * even hundreds of hooks registered for a single application.
			 * 
			 * While that behavior is unusual (usually an app has a few listeners
			 * attached to each event) it is possible that an application raising
			 * a certain event notifies many oher apps.
			 */
			foreach ($listeners as $listener) {
				$outbox = db()->table('outbox')->newRecord();
				$outbox->app = $listener->target;
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