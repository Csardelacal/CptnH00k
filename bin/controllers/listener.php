<?php

use spitfire\exceptions\HTTPMethodException;
use spitfire\exceptions\PublicException;
use spitfire\validation\ValidationException;

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

class ListenerController extends BaseController
{
	
	/**
	 * 
	 * @validate >> POST#hid(required string length[3, 50])
	 * @validate >> POST#app#Source(string required length[3, 50])
	 * @validate >> POST#listen#Trigger(string required length[3, 50])
	 * @validate >> POST#defer#Delay(positive number)
	 * @validate >> POST#transliteration(string) AND POST#url(required url)
	 * @validate >> POST#format(in[xml, json, nvp])
	 */
	public function register() {
		
		if (!$this->user && !$this->authapp) {
			throw new PublicException('Requires login', 403);
		}
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException(); }
			if (!$this->validation->isEmpty()) { throw new ValidationException('Validation failed', 0, $this->validation->toArray()); }
			
			$target = db()->table('authapp')->get('appID', $this->authapp? $this->authapp->getRemote()->getId() : $_POST['target'])->first();
			$record = db()->table('listener')->get('target', $target)->where('internalId', $_POST['hid'])->first()? : db()->table('listener')->newRecord();
			
			//TODO: Ensure that the apps exist
			
			$record->internalId = $_POST['hid'];
			$record->source = db()->table('authapp')->get('appID', $_POST['app'])->first();
			$record->target = $target;
			$record->listenTo = $_POST['listen'];
			$record->URL = $_POST['url'];
			$record->defer = $_POST['defer'];
			$record->format = $_POST['format'];
			
			$record->createdBy = $this->authapp? 'app:' . $this->authapp->getRemote()->getId() : 'user:' . (int)$this->user;
			$record->store();
			
			$this->view->set('success', $record);
		} 
		catch (ValidationException $e) {
			var_dump($e);
			die();
		}
		catch (HTTPMethodException $ex) {

		}
	}
	
	/**
	 * 
	 * @validate schedule (number positive)
	 * @validate defer (number positive)
	 * @validate trigger (required string length[3, 50])
	 */
	public function trigger() {
		
		if (!$this->authapp && !$this->user) {
			throw new PublicException('Insufficient privileges', 403);
		}
		
		if ($this->authapp) {
			$src = $this->authapp->getRemote()->getId();
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
		
		$payload = json_encode($_POST);
		$trigger = $_GET['trigger'];
		
		$record  = db()->table('inbox')->newRecord();
		$record->app = db()->table('authapp')->get('appID', $src)->first();
		$record->trigger = $trigger;
		$record->payload = $payload;
		$record->scheduled = (isset($_GET['schedule'])? $_GET['schedule'] : time()) + (isset($_GET['defer'])? $_GET['defer'] : 0);
		$record->store();
		
		$this->view->set('record', $record);
	}
	
	/**
	 * 
	 * 
	 */
	public function drop($id) {
		
		if (!$this->user && !$this->authapp) {
			throw new PublicException('Requires login', 403);
		}
		
		if ($this->authapp) {
			$listener = db()->table('listener')
				->get('app', db()->table('authapp')->get('appID', $this->authapp->getRemote()->getId()))
				->where('internalID', $id)
				->first(true);
		}
		else {
			$listener = db()->table()->get('_id', $id)->first(true);
		}
		
		$listener->delete();
		$this->view->set('listener', $listener);
	}
	
}