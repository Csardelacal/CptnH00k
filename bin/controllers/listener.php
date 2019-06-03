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
	public function edit(ListenerModel$l = null) {
		
		if (!$this->user && !$this->authapp) {
			throw new PublicException('Requires login', 403);
		}
		
		try {
			if (!$this->request->isPost()) { throw new HTTPMethodException(); }
			if (!$this->validation->isEmpty()) { throw new ValidationException('Validation failed', 0, $this->validation->toArray()); }
			
			/*
			 * Get the target application. If there is no target to be found, we
			 * will raise an exception.
			 * 
			 * It'd be interesting to ship CptnH00k with listeners for the Auth
			 * server, when app.* events happen, it could automatically refresh
			 * the application list.
			 */
			$authapp = $this->authapp? db()->table('authapp')->get('appID', $this->authapp->getRemote()->getId())->first() : null;
			$target  = db()->table('authapp')->get('appID', $authapp && !$authapp->isSSO? $this->authapp->getRemote()->getId() : $_POST['target'])->first(true);
			
			/*
			 * Check if the listener already exists for this application (to ensure
			 * that applications do request the same listener multiple times).
			 * 
			 * This should avoid programmers' need to check whether a hook already
			 * exists, they can just parse their recipe file, push the hooks and
			 * let h00k do the work.
			 */
			$record = $l? : (db()->table('listener')->get('target', $target)->where('internalId', $_POST['hid'])->first()? : db()->table('listener')->newRecord());
			
			/*
			 * Use the data submitted to register the hook. We make sure the data
			 * is validated by using Spitfire's validation mechanism in the 
			 * annotations.
			 */
			$record->internalId = $_POST['hid'];
			$record->source = db()->table('authapp')->get('appID', $_POST['app'])->first(true);
			$record->target = $target;
			$record->listenTo = $_POST['listen'];
			$record->URL = $_POST['url'];
			$record->defer = $_POST['defer'];
			$record->format = $_POST['format'];
			$record->transliteration = $_POST['transliteration']?? null;
			$record->createdBy = $this->authapp? 'app:' . $this->authapp->getRemote()->getId() : 'user:' . (int)$this->user;
			
			$record->store();
			
			$this->view->set('success', $record);
		} 
		/*
		 * Validation has failed. The application / user should be informed why
		 * this happened, the error messages are therefore passed onto the view.
		 */
		catch (ValidationException $e) {
			$this->view->set('messages', $e->getResult());
		}
		catch (HTTPMethodException $ex) {
			if ($this->authapp) { 
				throw new PublicExeption('Invalid request method. Apps cannot GET this endpoint', 400); 
			}
		}
		
		$this->view->set('listener', $l);
	}
	
	/**
	 * This endpoint allows the SSO server to query the listeners registered on
	 * the applications
	 * 
	 * @param string $p
	 * @param string $_
	 * @throws PublicException
	 */
	public function registered($p, $_ = null) {
		
		if ($this->authapp) {
			
			$sso = db()->table('authapp')->get('appID', $this->authapp->getSrc()->getId())->first(true);
			
			if (!$sso || !$sso->isSSO) {
				throw new PublicException('Application cannot list listeners, please refer to your authentication server', 403);
			}
		}
		elseif ($this->user) {
			//If a user is logged into the application, it means they're an administrator
		}
		else {
			throw new PublicException('Invalid signature received.', 403);
		}
		
		$query = db()->table('listener')->getAll();
		
		foreach (array_filter([$p, $_]) as $param) {
			$pieces = explode(':', $param);
			
			if (count($pieces) !== 2) {
				throw new PublicException('Invalid parameter', 400);
			}
			
			list($role, $appID) = $pieces;
			
			$app = db()->table('authapp')->get('appID', $appID)->first(true);
			$query = db()->table('listener')->get($role === 'from'? 'source' : 'target', $app);
			
			$this->view->set($role === 'from'? 'source' : 'target', $app);
		}
		
		$listeners = $query->all();
		
		$this->view->set('selectedApp', $app->appID);
		$this->view->set('role', $role);
		$this->view->set('listeners', $listeners);
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
			$listener = db()->table('listener')->get('_id', $id)->first(true);
		}
		
		$listener->delete();
		$this->view->set('listener', $listener);
	}
	
}