<?php
require_once(t3lib_extMgm::extPath('ods_osm').'class.tx_odsosm_div.php');

class tx_odsosm_tcemain {
	var $lon=array();
	var $lat=array();

	// ['t3lib/class.t3lib_tcemain.php']['processDatamapClass']
	function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $obj){
	}

	// ['t3lib/class.t3lib_tcemain.php']['processDatamapClass']
	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $obj) {
		switch($table){
			case 'fe_users':
			case 'tt_address':
				$config=unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ods_osm']);

				// Search coordinates
				if($config['autocomplete'] && ($fieldArray['zip'] || $fieldArray['city'])){
					$address=$obj->datamap[$table][$id];
					if($config['autocomplete']==2 || floatval($address['tx_odsosm_lon'])==0){
						$ll=tx_odsosm_div::updateAddress($address);
						if($ll){
							$fieldArray['tx_odsosm_lon']=sprintf('%01.6f',$address['lon']);
							$fieldArray['tx_odsosm_lat']=sprintf('%01.6f',$address['lat']);
							$fieldArray['zip']=$address['zip'];
							$fieldArray['city']=$address['city'];
							$fieldArray['country']=$address['country'];
						}
					}
				}
			break;

			case 'tx_odsosm_track':
				$filename=PATH_site.'uploads/tx_odsosm/'.$fieldArray['file'];
				if($fieldArray['file'] && file_exists($filename)){
					require_once t3lib_extMgm::extPath('ods_osm','res/geoPHP/geoPHP.inc');
					$polygon = geoPHP::load(file_get_contents($filename),pathinfo($filename,PATHINFO_EXTENSION));
					$box = $polygon->getBBox();
				}
				$fieldArray['min_lon']=sprintf('%01.6f',$box['minx']);
				$fieldArray['min_lat']=sprintf('%01.6f',$box['miny']);
				$fieldArray['max_lon']=sprintf('%01.6f',$box['maxx']);
				$fieldArray['max_lat']=sprintf('%01.6f',$box['maxy']);
			break;

			case 'tx_odsosm_marker':
				if($fieldArray['icon'] && file_exists(PATH_site.'uploads/tx_odsosm/'.$fieldArray['icon'])){
					$size=getimagesize(PATH_site.'uploads/tx_odsosm/'.$fieldArray['icon']);
					$fieldArray['size_x']=$size[0];
					$fieldArray['size_y']=$size[1];
					$fieldArray['offset_x']=-round($size[0]/2);
					$fieldArray['offset_y']=-$size[1];
				}
			break;

			case 'tx_odsosm_vector':
				if($fieldArray['data']){
					$this->lon=array();
					$this->lat=array();

					$vector=json_decode($fieldArray['data']);
					foreach($vector->geometry->coordinates[0] as $coordinates){
						$this->lon[]=$coordinates[0];
						$this->lat[]=$coordinates[1];
					}
				}
				$fieldArray['min_lon']=sprintf('%01.6f',min($this->lon));
				$fieldArray['min_lat']=sprintf('%01.6f',min($this->lat));
				$fieldArray['max_lon']=sprintf('%01.6f',max($this->lon));
				$fieldArray['max_lat']=sprintf('%01.6f',max($this->lat));
			break;
		}
	}
}
?>