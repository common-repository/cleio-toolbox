<?php
abstract class CleioToolbox_Helpers {
	/**
	 * Check if the Cleio base menu already exists
	 * @return boolean Return true if the menu already exist
	 */
	function checkToolboxMenu()
	{
		global $submenu;
		$exist = false;
		if( $submenu['cleio-base'] ) {
			foreach($submenu['cleio-base'] as $item) { if(strtolower($item[2]) == strtolower('cleio-toolbox')) { $exist = true; } }
		}
		return $exist;
	}
}
?>