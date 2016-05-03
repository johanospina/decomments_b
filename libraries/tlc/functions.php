<?php

// API so you don't have to use "new"
if ( ! function_exists( 'decomments_tlc_transient' ) ) {
	function decomments_tlc_transient( $key ) {
		$transient = new TLC_Transient( $key );

		return $transient;
	}
}