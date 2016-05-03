<?php

interface DECOM_Loadable {
	/**
	 * Invokes on plugin activation
	 *
	 * @return mixed
	 */
	public static function onActivation();

	/**
	 * Invokes on plugin deactivation
	 *
	 * @return mixed
	 */
	public static function onDeactivation();

	/**
	 * Invokes on 'init' wordpress hook
	 *
	 * @return mixed
	 */
	public static function onAutoInclude( $mode = '' );

	/**
	 * Invokes on including by prompt
	 *
	 * @return mixed
	 */
	public static function onManualInclude( $mode = '' );
}

class DECOM_Library implements DECOM_Loadable {
	/**
	 * Invokes on plugin activation
	 *
	 * @return mixed
	 */
	public static function onActivation() {

	}

	/**
	 * Invokes on plugin deactivation
	 *
	 * @return mixed
	 */
	public static function onDeactivation() {

	}

	/**
	 * Invokes on 'init' wordpress hook
	 *
	 * @return mixed
	 */
	public static function onAutoInclude( $mode = '' ) {

	}

	/**
	 * Invokes on including by prompt
	 *
	 * @return mixed
	 */
	public static function onManualInclude( $mode = '' ) {

	}
}