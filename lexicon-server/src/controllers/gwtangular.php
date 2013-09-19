<?php
use models\rights\Operation;
use models\rights\Domain;
use models\rights\Realm;
use models\rights\Roles;
use models\ProjectListModel;

require_once 'app.php';
class gwtangular extends App {
	
	public function view($app = 'main', $param1 = '', $param2 = '') {
		
		// some "favicon.ico" pass into here, find it out
		if (strpos ( $param1, '.' ) !== false) {
			error_log ( $param1 . " is not a valid id" );
			return;
		}
		
		if (strpos ( $param2, '.' ) !== false) {
			error_log ( $param2 . " is not a valid id" );
			return;
		}
		
		$data = array ();
		$data ['project_id'] = $param1;
		$data ['title'] = "Language Forge";
		$data ['is_static_page'] = true;
		$data ['logged_in'] = $this->_isLoggedIn;
		if ($this->_isLoggedIn) {
			$isAdmin = Roles::hasRight ( Realm::SITE, $this->_user->role, Domain::USERS + Operation::CREATE );
			$data ['is_admin'] = $isAdmin;
			$data ['user_name'] = $data ['user_name'] = $this->_user->username;
			$data ['user_id'] = $this->_user->id;
			$data ['small_gravatar_url'] = $this->ion_auth->get_gravatar ( "30" );
			$data ['small_avatar_url'] = $this->_user->avatar_ref;
		}
		// Angular App view
		$data = $this->prepareData($app,$data, $param1, $param2);
		$view="angular-app";
		if (file_exists(APPPATH . "/views/" . $view . ".html.php")) {
			$view = $view . ".html.php";
		}
		$data["page"] = $view;
		if (! file_exists ( 'views/gwtpages/gwtangularcontainer.html.php' )) {
			show_404 ();
		} else {
			return $this->load->view ('gwtpages/gwtangularcontainer.html.php', $data, false );
		}
	}

}

?>