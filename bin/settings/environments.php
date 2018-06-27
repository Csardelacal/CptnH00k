<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('test');
$e->set('db', 'mysqlpdo://root:@localhost:3306/cptnh00k?encoding=utf8&prefix=test_');
$e->set('SSO', 'http://60359639:FUgJefNDvypTJm3VkYjWPp23HXQi09taBfASw5IbNzVGtMs@localhost/PHPAuthServer/');

$e->set('debugging_mode', true);
$e->set('debug_mode', true);