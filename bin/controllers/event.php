<?php

/* 
 * The MIT License
 *
 * Copyright 2019 César de la Cal Bretschneider <cesar@magic3w.com>.
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

class EventController extends BaseController
{
	
	
	/**
	 * 
	 * @validate schedule (number positive)
	 * @validate defer (number positive)
	 * @validate event (required string length[3, 50])
	 */
	public function push() {
		
		if (!$this->authapp && !$this->user) {
			throw new PublicException('Insufficient privileges', 403);
		}
				
		if ($this->authapp && $this->authapp->getRemote()) {
			$src = $this->authapp->getRemote()->getId();
		}
		/*
		 * The application does not provide a remote source, this would imply that
		 * the other party is the SSO server (since it is the only app that does 
		 * not require signing)
		 */
		elseif ($this->authapp && $this->authapp->getSrc()->getId() == $this->sso->getAppId()) {
			$src = $this->authapp->getSrc()->getId();
		}
		elseif ($_GET['psk']) {
			//TODO Define behavior when an application with a pre-shared-key is pushing an event
		}
		elseif($this->user && isset($_POST['authapp'])) {
			$src = $_POST['authapp'];
		}
		else {
			throw new PublicException('No application defined', 403);
		}
		
		/*
		 * Spitfire will automatically handle JSON and XML payloads and convert 
		 * them to post, this way the application can just store the _POST variable
		 * and use it later, when placing it in the outbox.
		 */
		$payload = json_encode($_POST);
		
		/*
		 * Identify the trigger that was sent from the app. This allows applications
		 * to listen for certain events within the source application instead of
		 * listening to all events.
		 */
		$trigger = $_GET['event'];
		
		$record  = db()->table('inbox')->newRecord();
		$record->app = db()->table('authapp')->get('appID', $src)->first();
		$record->trigger = $trigger;
		$record->payload = $payload;
		$record->scheduled = (isset($_GET['schedule'])? $_GET['schedule'] : time()) + (isset($_GET['defer'])? $_GET['defer'] : 0);
		$record->store();
		
		$this->view->set('record', $record);
	}
}