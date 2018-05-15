<?php

/**
 * MY_CONTROLLER extension for THE FORUM
 *
 *
 * @property boolean $isDataPost are the data sent by post
 * @property boolean $touchInterface is the user with a touch interface device
 * @property \Forum_model $oForum
 *
 * @author Massieur Hunter
 */
class Forum_Controller extends MY_Controller
{

	/**
	 * Response sent to the user
	 *
	 * @var string
	 */
	protected $response;

	/**
	 *
	 * @var boolean
	 */
	protected $isDataPost;

	/**
	 *
	 * @var boolean
	 */
	protected $touchInterface;


	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		$oMobiledetect			 = new \Detection\MobileDetect();
		$this->csrfTokenName	 = $this->security->get_csrf_token_name();
		$this->csrfHash			 = $this->security->get_csrf_hash();
		$this->touchInterface	 = $oMobiledetect->isMobile() || $oMobiledetect->isTablet();
		$this->isDataPost		 = !empty($this->input->post());

		$this->load->model('forum/forum_model', 'oForum');

		$this->load->library('user_agent');

//		$this->updateWhoIsOnline();

		if (ENVIRONMENT == 'development') {
			$this->template->setVar('domain', str_replace(array(
				'https',
				'www.eapc2.com'), array(
				'http',
				'local.eapc2.com'), substr($this->oForum->ge, 0, -1)));
		} else {
			$this->template->setVar('domain', $this->oWebsite->getCorrectDomain());
		}

		/* custom CSS */
/*		$customCSSName = $this->oWebsite->getID() . "_customsheet.css";

		if (file_exists(CUSTOM_CSS_PATH . $customCSSName)) {
			$fileModificationTime		 = filemtime(CUSTOM_CSS_PATH . $this->oWebsite->getID() . "_customsheet.css");
			$customCSSNameWithVersion	 = $this->oWebsite->getID() . "_customsheet.css?v=$fileModificationTime";
			$customCSS					 = '<link rel="stylesheet" href="/css/custom/' . $customCSSNameWithVersion . '">';
		}
*/
		/*
		 * Update user login infos
		 */
		if ($this->oCurrentUser->getID()) {
			$this->oCurrentUser->createCookie();
//			$this->oWebsite->updateTablesForUserLogin($this->oCurrentUser);
		}

		/* Update website stats (visits count) */
		$this->oWebsite->updateStats();

		$this->template
			->setVar('baseUrl', $this->oWebsite->getCorrectBaseUrl())
			->setVar('staticUrl', STATIC_URL)
			->setVar('touchInterface', $this->touchInterface)
			->setVar('tokenName', $this->csrfTokenName)
			->setVar('tokenHash', $this->csrfHash)
			->setVar('isDev', ENVIRONMENT == 'development')
			->setVar('isProd', ENVIRONMENT == 'production')
			->setVar('isLoggedIn', $this->oCurrentUser->getID() != null)
			->setVar('isAdmin', $this->oCurrentUser->isAnyAdmin())
			->setVar('isPost', $this->isDataPost)
//			->setVar('designID', $this->oWebsite->getFkDesignID())
			->setVar('isEdge', isMsEdge() || isIE())
			->setVar('isIE', isIE())
			->setVar('isIE8', isIE8())
			->setVar('isIE9', isIE9())
			->setVar('isIE10', isIE10())
			->setVar('isIE11', isIE11())
			->setVar('googleAnalytics', $this->getGoogleAnalyticsScript())
			->setVar('googleAnalyticsForTest', $this->getGoogleAnalyticsScriptForTest())
			->setVar('customCSS', isset($customCSS) ? $customCSS : null)
			->setVar('copyrightFooter', $this->template->saveInVar('copyright_footer'))
		;

	}

	/**
	 * send the response to the user
	 * in a json object
	 * or in plain html
	 * depending of the "json" post request
	 */
	protected function htmlOrJson() {
		if ($this->isDataPost) {
			$response = array(
				'html' => $this->response);
			$this->sendJson($response);
		} else {
			echo $this->response;
		}
	}

	/*
	 *
	 *
	 * WHO IS ONLINE
	 *
	 *
	 */

	public function updateWhoIsOnline() {
		$this->oWebsite->updateWhoIsOnline();
	}

	/*
	 *
	 *
	 * Google analytics script
	 *
	 *
	 */

	public function getGoogleAnalyticsScript() {
		return $this->template->saveInVar('googleAnalytics');
	}

	public function getGoogleAnalyticsScriptForTest() {// for test server
		return $this->template->saveInVar('googleAnalyticsForTest');
	}

}
