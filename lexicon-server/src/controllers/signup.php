<?php 

use models\rights\Realm;
use models\rights\Roles;

require_once 'base.php';

class Signup extends Base {
	
	public function view() {
		
		$app = 'signup';
		
		$data = array();
		$data['appName'] = $app;
		
		$data['jsCommonFiles'] = array();
		self::addJavascriptFiles("angular-app/common/js", $data['jsCommonFiles']);
		$data['jsProjectFiles'] = array();
		self::addJavascriptFiles("angular-app/$app", $data['jsProjectFiles']);
			
		$data['title'] = "Language Forge";
		$data['jsonSession'] = '"";'; // empty json session data that angular-app template needs to be happy
		
		$this->_render_page("angular-app", $data);
	}
	
	private static function ext($filename) {
		return pathinfo($filename, PATHINFO_EXTENSION);
	}
	
	private static function addJavascriptFiles($dir, &$result) {
		if (($handle = opendir($dir))) {
			while ($file = readdir($handle)) {
				if (is_file($dir . '/' . $file)) {
					if (self::ext($file) == 'js') {
						$result[] = $dir . '/' . $file;
					}
				} elseif ($file != '..' && $file != '.') {
					self::addJavascriptFiles($dir . '/' . $file, $result);
				}
			}
			closedir($handle);
		}
	}
}

?>