<?php

class DECOM_WooUrlHelper {
	// input http://mail.somedomain.co.uk - outputs 'somedomain.co.uk'
	public static function getDomainFromUrl( $url ) {
		$pieces = parse_url( $url );
		$domain = isset( $pieces['host'] ) ? $pieces['host'] : $pieces['path'];
		if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs ) ) {
			return $regs['domain'];
		}

		return false;
	}

	public static function getDomain() {
		return self::getDomainFromUrl( self::getHost() );
	}

	public static function getHost() {
		$host = false;

		if ( array_key_exists( 'HTTP_HOST', $_SERVER ) ) {
			if ( isset( $_SERVER['HTTP_HOST'] ) ) {
				$host = $_SERVER['HTTP_HOST'];
			}
		} else {
			if ( array_key_exists( 'SERVER_NAME', $_SERVER ) ) {
				if ( isset( $_SERVER['SERVER_NAME'] ) ) {
					$host = $_SERVER['SERVER_NAME'];
				}
			}
		}

		return $host;
	}
}