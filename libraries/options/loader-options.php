<?php

class DECOM_Loader_Options extends DECOM_Loader {
	public static function getInstance() {
		return DECOM_Loader_MVC::getLibraryModel( 'options', 'options' );
	}
}