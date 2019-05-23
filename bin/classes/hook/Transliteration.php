<?php namespace hook;

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

/**
 * This class allows the application to transliterate requests from one format to
 * another - allowing to reduce friction between applications that connect with each other.
 * 
 * Making use of this class allows to connect two potentially incompatible API
 * with each other (as long as the data required is provided)
 * 
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
class Transliteration
{
	
	/**
	 * The data that the source application sends. CPTNH00K will always convert 
	 * incoming data to JSON to ensure compatibility. So, whether the application
	 * posted a regular form, JSON or XML - we will receive JSON here.
	 *
	 * @var mixed
	 */
	private $data;
	
	/**
	 * The mask is a multi-layered array of strings that defines how the data should
	 * be provided to a target application. Please note that the mask won't convert
	 * data to any format, but just rearrange the data so it makes sense to the 
	 * target app.
	 * 
	 * For example, assume that the application A sends an event like this:
	 * <code>{ user : 1, action : update }</code>
	 * 
	 * But the target application expects it like:
	 * <code>{ payload : { data : user, id : 1 } , action : updated }</code>
	 * 
	 * We can quickly write a mask that looks like this:
	 * <code>{ payload : { data : user, id : @user } , action : updated }</code>
	 * 
	 * And CptnH00k will automatically convert our requests to the specified target
	 * format. This allows us to connect two applications that are not compatible
	 * without changing the code within them nor creating an extra application.
	 * 
	 * Payloads are expected to be small and simple, so transliteration is not
	 * a complicated process.
	 *
	 * @var mixed
	 */
	private $mask;
	
	/**
	 * Creates a transliteration. Converting the data to the format provided by the
	 * mask.
	 * 
	 * @param mixed $data
	 * @param mixed $mask
	 */
	public function __construct($data, $mask) {
		$this->data = $data;
		$this->mask = $mask;
	}
	
	/**
	 * Executes the transliteration.
	 * 
	 * @return string
	 */
	public function transliterate() {
		return json_encode($this->iterate(json_decode($this->data), json_decode($this->mask, true)));
	}
	
	/**
	 * Walk over the mask recursively to generate the output. This function works
	 * with arrays and is interfaced by the transliterate() method.
	 * 
	 * @todo Consider the option to add some kind of "function" or procedure mechanism
	 * to allow people to create basic compound statements when writing transliteration.
	 * 
	 * @param mixed $data
	 * @param mixed $mask
	 * @return mixed
	 */
	private function iterate($data, $mask) {
		 /*
		  * This is the recursive part of the loop, if the data is an array, we descend
		  * into the leaves of it, to transliterate the strings in it.
		  */
		if (is_array($mask)) {
			return collect($mask)->each(function ($e) use ($data) { return $this->iterate($data, $e); })->toArray();
		}
		/*
		 * If the string is preceeded by an @ symbol, it means that the mask expects
		 * the data in this field to be converted.
		 * 
		 * The user may provide a '.' separated list to indicate which value they 
		 * wish to receive. Please note that this means that your JSON should not
		 * contain stops in the name of the maps.
		 * 
		 * While it is technically possible to do so, it's rather uncommon and looks off.
		 */
		elseif(\Strings::startsWith($mask, '@')) {
			$pieces = explode('.', substr($mask, 1));
			$t = $data;
			
			foreach ($pieces as $piece) {
				$t = $t->$piece;
			}
			
			return $t;
		}
		/*
		 * Last but not least, if the mask expects a literal to be returned, then 
		 * we return the value just like the mask expected it.
		 */
		else {
			return $mask;
		}
	}
	
	/**
	 * Creates a transliteration. Converting the data to the format provided by the
	 * mask.
	 * 
	 * @param mixed $data
	 * @param mixed $mask
	 */
	public static function instance($data, $mask) {
		return new Transliteration($data, $mask);
	}
	
}
