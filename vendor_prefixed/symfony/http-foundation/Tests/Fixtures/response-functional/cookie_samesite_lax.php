<?php
/**
 * @license MIT
 *
 * Modified by gravityview on 14-December-2022 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

use GravityKit\GravityView\Symfony\Component\HttpFoundation\Cookie;

$r = require __DIR__.'/common.inc';

$r->headers->setCookie(new Cookie('CookieSamesiteLaxTest', 'LaxValue', 0, '/', null, false, true, false, Cookie::SAMESITE_LAX));
$r->sendHeaders();
