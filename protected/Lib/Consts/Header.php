<?php

namespace Consts;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> http://namedin.com <tondarweb@gmail.com> http://webdesignir.com
 * @package Tondarweb Portal
 * @version 2
 * @copyright (c) Abbas Ali Hashemian
 * @access public
 */
class Header {

	const CacheControl = "Cache-Control: "
			, Cache_NoCache = "no-store, no-cache, must-revalidate, post-check=0, pre-check=0";
	const ContentType = "Content-type: "
			, HTMLCharSet = "Content-type: text/html; charset="
			, TextPlain="Content-type: text/plain";
	const Redirect = 'HTTP/1.1 302 Found'
			, RedirectCode = 302
			, PermanentRedirect = '301 Moved Permanently'
			, PermanentRedirectCode = 301
			, Location = 'Location: ';
	const NotFound = 'HTTP/1.1 404 Not Found'
			, NotFoundCode = 404;
	const Forbidden = 'HTTP/1.1 403 Forbidden'
			, ForbiddenCode = 403;
	const NotModified = 'HTTP/1.1 304 Not Modified'
			, LastModify = 'Last-Modified: '
			, NotModifiedCode = 304;
	const Expires = 'Expires: ';
	const ServerInternalError = 'HTTP/1.1 500 Internal Server Error'
			, ServerInternalErrorCode = 500;
	const BadRequest = 'HTTP/1.1 400 Bad Request'
			, BadRequestCode = 400;

}

?>
