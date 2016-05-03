<?php

interface DECOM_Installable {
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
}


class DECOM_Component implements DECOM_Installable {
	public static function onActivation() {

	}

	public static function onDeactivation() {

	}

	public static function onComponentInclude() {
		return true;
	}
}