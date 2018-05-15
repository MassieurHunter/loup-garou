<?php

/**
 * Description of Timeline
 *
 * @property \Timeline_model $oTimeline
 * @property \Countriesmanager_model $oCountriesManager
 * @property \Element_model $oElementImage
 * @property \Element_model $oElementText
 * @property \Element_model $oElementVideo
 * @author Ariel De Paz <depaz@sport50.com>
 */
class Timeline extends CMS_Controller
{

	public function __construct() {
		$this->arrTranslationPages = array(
			'elements',
			'calendar',
			'timeline',
			'news',
			'TeamManagement',
			'leagueManagement',
			'standings',
			'cms_calendar',
			'cms_adm_mnggames',
			'cms_adm_mngemgrps',
			'cms_adm_mngroster',
			'cms_scene',
			'cms_gamesheet',
			'cms_attendance',
			'*cms_results',
		);
		
		$this->appID = 42;

		parent::__construct();

		$this->load->model('website/timeline_model', 'oTimeline', false, $this->oWebsite);
		$this->load->model('language/countriesmanager_model', 'oCountriesManager');

		$this->template
				->setVar('transTimeline', $this->translationsTimeline->getTranslations())
				->setVar('transTeam', $this->translationsTeamManagement->getTranslations())
				->setVar('transLeague', $this->translationsLeagueManagement->getTranslations())
				->setVar('transStandings', $this->translationsStandings->getTranslations())
				->setVar('transCalendar', $this->translationsCms_calendar->getTranslations())
				->setVar('transMultiScenes', $this->translationsCms_adm_mnggames->getTranslations())
				->setVar('transGroups', $this->translationsCms_adm_mngemgrps->getTranslations())
				->setVar('transRoster', $this->translationsCms_adm_mngroster->getTranslations())
				->setVar('transScene', $this->translationsCms_scene->getTranslations())
				->setVar('transResults', $this->translationsCms_results->getTranslations())
				->setVar('transGamesheet', $this->translationsCms_gamesheet->getTranslations())
				->setVar('transAttendance', $this->translationsCms_attendance->getTranslations())
				->setVar('transElements', $this->translationsElements->getTranslations())
				->setVar('transCalendar', $this->translationsCalendar->getTranslations())
				->setVar('tansNews', $this->translationsNews->getTranslations())
				->setVar('isFederation', $this->oClub->isFederationAdmin())
				->setVar('moduleUrl', 'timeline/')
				->setVar('season', $this->oClub->getOSport()->getCurrentYearForSeason())
				->setVar('newsPicUrl', BASE_URL_IMAGE_NEWS)
				->setVar('leaguePicUrl', BASE_URL_IMAGE_LEAGUES)
				->setVar('playerPicUrl', BASE_URL_IMAGE_PLAYER)
				->setVar('teamLogoUrl', PATH_OLD_TEAM)
				->setVar('uploadedPicUrl', BASE_URL_UPLOAD_IMAGES)
				->setVar('sponsorPicUrl', BASE_URL_SPONSORS)
				->setVar('eventPicUrl', BASE_URL_CALENDAR_IMAGES)
				->setVar('clubTeams', $this->oClub->getAllTeamsWithVeryBasicInfos())
				->setVar('pagesNavigation', $this->navigation())
		;

		if ($this->oCurrentUser->isGamesAdmin()) {
			$this->template
					->setVar('arrForfeitScore', $this->oClub->getOSport()->getForfeitScore())
					->setVar('addGameDialog', $this->addGameDialog())
					->setVar('addMultipleGamesDialog', $this->addMultipleGamesDialog())
					->setVar('firstClickTeamsBox', $this->firstClickTeamsBox())
					->setVar('noTeamFoundBox', $this->noTeamFoundBox())
					->setVar('firstClickArenasBox', $this->firstClickArenasBox())
					->setVar('noArenaFoundBox', $this->noArenaFoundBox())
					->setVar('firstClickLeaguesBox', $this->firstClickLeaguesBox())
					->setVar('noLeagueFoundBox', $this->noLeagueFoundBox())
					->setVar('addEvent', $this->addEvent())
			;
		}

		if ($this->oCurrentUser->isPageAdmin()) {
			$this->template
					->setVar('addImageForm', $this->addImageForm())
					->setVar('addTextForm', $this->addTextForm())
					->setVar('addTipGameForm', $this->addTipGameForm())
			;
		}

		if ($this->oCurrentUser->isNewsAdmin()) {
			if ($this->langText == 'fr') {
				$this->template->setVar('wysibbLanguage', 'fr');
			} elseif ($this->langText == 'de') {
				$this->template->setVar('wysibbLanguage', 'de');
			}
		}
		
		if (!$this->oWebsite->isDisplayed() && !$this->oCurrentUser->isSuperAdmin()) {
			$this->template->display('pagemanager/visitorOff');
			die;
		}
		
		if (!$this->oWebsite->isTimelineActive() && !$this->oCurrentUser->isSuperAdmin()){
			echo 'timeline not activated';
			die;
		}
	}

	/*
	 * Method reachables by URL
	 */

	/**
	 * Default method is displayTimeline
	 */
	public function index() {
		$this->displayTimeline();
	}

	/**
	 * Display the timeline
	 */
	public function displayTimeline() {
		$openElementUniqueID = $this->input->get('openElement');

		$arrTimelineInfos = $this->oTimeline->getTimeline(false, $openElementUniqueID);

		$arrElements	 = $arrTimelineInfos['elements'];
		$nbTotalPages	 = $arrTimelineInfos['nbPages'];

		if ($openElementUniqueID) {
			foreach ($arrElements as $element) {
				if ($element['uniqueID'] == $openElementUniqueID) {
					$openElementDetails	 = explode('-', $element['detailsInfos']);
					$type				 = $openElementDetails[0];
					$id					 = $openElementDetails[1];
					$extraInfo			 = isset($openElementDetails[2]) ? $openElementDetails[2] : false;
					$extraInfo2			 = isset($openElementDetails[3]) ? $openElementDetails[3] : false;
					break;
				}
			}

			$elementDetails = $this->oTimeline->generateElementDetails($type, $id, $extraInfo, $extraInfo2);

			$this->template
					->setVar('element', $elementDetails['element'])
					->setVar('relatedElements', $elementDetails['relatedElements']);
		}

		$this->template
				->setVar('nbTotalPages', $nbTotalPages)
				->setVar('mainContent', $this->getTimelineContent($arrElements))
				->display('timeline/index');
	}

	/**
	 * Admin panel
	 */
	public function adminPanel() {
		if ($this->oCurrentUser->isSuperAdmin()) {
			$this->template
					->setVar('navigationBar', $this->navigationBarForConfPanel())
					->setVar('mainContent', $this->timelineConfPanel())
					->display('timeline/admin/index');
		}
	}
	

	/*
	 * HTML functions
	 */
	
	protected function navigation() {
		$arrPages = $this->oWebsite->getArrPagesWithHierachyAndBasicInfos(false, $this->oCurrentUser->getUserID());

		$pageList = '';
		foreach ($arrPages as $page) {
			$pageList .= $this->pageHtml($page);
		}
				
		$onTimeline = false;
		if (strstr($this->uri->ruri_string(), 'timeline')) {
			$onTimeline = true;	
		}

		return $this->template
						->setVar('pageList', $pageList)
						->setVar('onTimeline', $onTimeline)
						->saveInVar('page/pagesNavigation');
	}
	
	private function pageHtml($page) {
		$children = '';
		if (isset($page['children'])) {
			foreach ($page['children'] as $child) {
				$children .= $this->pageHtml($child);
			}
		}
		if(!isset($page['pageID'])){
			$page['pageID'] = '';
		}
		return $this->template
						->setVar('page', $page)
						->setVar('pageID', $page['pageID'])
						->setVar('children', $children)
						->saveInVar('page/navbarOptions')
		;
	}

	/**
	 * configuration pannel for the timeline
	 *
	 * @return string
	 */
	protected function timelineConfPanel() {
		/*
		 * Normal club
		 */
		if (!$this->oClub->isFederationAdmin()) {
			$this->template
					->setVar('clubTeams', $this->oClub->getAllTeamsWithVeryBasicInfos())
					->setVar('timelineTeams', $this->oTimeline->getArrWebsiteTeams())
					->setVar('timelineTeamsConf', $this->oTimeline->getWebsiteTeamsElementsConf());
		} else {
			/*
			 * federation
			 */
			$this->template
					->setVar('clubLeagues', $this->oClub->getLeaguesWithBasicInfos())
					->setVar('timelineLeagues', $this->oTimeline->getArrWebsiteLeagues())
					->setVar('timelineLeaguesConf', $this->oTimeline->getWebsiteLeaguesElementsConf());
		}

		return $this->template
						->setVar('timelineElements', $this->oTimeline->getAllElements())
						->setVar('timelineElementsConf', $this->oTimeline->getWebsiteElementsConf())
						->setVar('timelineBPS', $this->oTimeline->getWebsiteBPS())
						->setVar('TYPE_STANDINGS', Timeline_model::ELEMENT_STANDINGS)
						->saveInVar('timeline/admin/inc/timelineConfPanel');
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function navigationBarForConfPanel(){
		$arrInfos			 = array();
		$arrInfos['h1']		 = $this->translationsTimeline->getTranslation('timelineAdmin');
		$arrInfos['h1Link']	 = array(
			'activated'	 => false,
		);
		
		/*
		 * Navigation menu
		 */
		$arrInfos['navigationMenu']		 = array();
		$arrInfos['navigationMenu'][0]	 = array(
			'link'	 => 'manager/dashboard/apps/group/' . $this->oCurrentApp->getAppGroupID(),
			'title'	 => $this->oCurrentApp->getOAppGroup()->getNameTranslated(),
		);
		
		$arrInfos['navigationMenu'][1]	 = array(
			'link'	 => false,
			'title'	 => $this->translationsTimeline->getTranslation('timelineAdmin'),
		);
		/*
		 * Generate the top menu
		 */
		$arrInfos['topMenu'] = array();
		/*
		 * Generate the breadcrum
		 */
		$arrInfos['breadCrum'] = array();

		/*
		 * Generate the menu and returns it
		 */
		return $this->navigationBar($arrInfos);
	}

	/**
	 * Return html code for the timeline based on an array of elements
	 *
	 * @param array $arrElements
	 * @return string
	 */
	protected function getTimelineContent($arrElements) {
		$elementsHtml = '';
		foreach ($arrElements as $element) {
			$elementsHtml .= $this->oTimeline->generateHtmlForElement($element);
		}

		return $this->template
						->setVar('elmentsHtml', $elementsHtml)
						->saveInVar('timeline/inc/timelineContent');
	}

	/*
	 *
	 *
	 * ADD ELEMENTS DIALOGS
	 *
	 *
	 */

	/*
	 * Games dialogs
	 */

	/**
	 *
	 * @return string
	 */
	private function noTeamFoundBox() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/noTeamFoundBox');
	}

	/**
	 *
	 * @return string
	 */
	private function noLeagueFoundBox() {
		return $this->template->saveInVar('leagueManager/inc/dialogAndAutocomplete/noLeagueFoundBox');
	}

	/**
	 *
	 * @return string
	 */
	private function noArenaFoundBox() {
		return $this->template->saveInVar('leagueManager/inc/dialogAndAutocomplete/noArenaFoundBox');
	}

	/**
	 *
	 * @return string
	 */
	private function addGameDialog() {
		return $this->template->saveInVar('leagueManager/inc/dialogAndAutocomplete/addGameDialog');
	}

	/**
	 *
	 * @return string
	 */
	private function addMultipleGamesDialog() {
		return $this->template->saveInVar('leagueManager/inc/dialogAndAutocomplete/addMultipleGamesDialog');
	}

	/**
	 *
	 * @return string
	 */
	private function firstClickLeaguesBox() {
		return $this->template
						->setVar('arrAdminLeagues', $this->oClub->getLeaguesAdminWithBasicInfos($this->oClub->getSportID()))
						->saveInVar('leagueManager/inc/dialogAndAutocomplete/firstClickLeaguesBox');
	}

	/**
	 *
	 * @return string
	 */
	private function firstClickTeamsBox() {
		$arrClubTeams		 = $this->oClub->getAllTeamsWithVeryBasicInfos();
		$arrEditablesTeams	 = $this->oClub->getEditablesTeamsWithVeryBasicInfos();
		$arrOpponents		 = $this->oClub->getAllOpponentsTeamsWithVeryBasicInfos();

		foreach ($arrClubTeams as $teamID => $teamID) {
			$arrClubTeams[$teamID]['isEditable'] = isset($arrEditablesTeams[$teamID]);
		}
		return $this->template
						->setVar('arrClubTeams', $arrClubTeams)
						->setVar('arrOpponents', $arrOpponents)
						->saveInVar('teamManager/inc/dialogAndAutocomplete/firstClickTeamsBox');
	}

	/**
	 *
	 * @return string
	 */
	private function firstClickArenasBox() {
		$arrArenas = $this->oClub->getUsedArenasWithBasicInfos();

		return $this->template
						->setVar('arrArenas', $arrArenas)
						->saveInVar('leagueManager/inc/dialogAndAutocomplete/firstClickArenasBox');
	}

	private function addEvent() {
		return $this
						->template
						->setVar('countries', $this->oCountriesManager->getAllCountriesWithBasicInfos())
						->saveInVar('calendar/add/events');
	}

	/*
	 * elements form
	 */

	private function addImageForm() {
		$this->load->model('elements/element_model', 'oElementImage');
		$this->oElementImage
				->setType(Element_model::TYPE_IMAGE)
				->setFkwebsiteID($this->oWebsite->getID())
		;

		return $this->oElementImage->getHtmlForForm(false);
	}

	private function addTextForm() {
		$this->load->model('elements/element_model', 'oElementText');
		$this->oElementText
				->setType(Element_model::TYPE_TEXT)
				->setFkwebsiteID($this->oWebsite->getID())
		;

		return $this->oElementText->getHtmlForForm(false);
	}

	private function addTipGameForm() {
		$this->load->model('elements/element_model', 'oElementTipGame');
		$this->oElementTipGame
				->setType(Element_model::TYPE_TIP_GAME)
				->setFkwebsiteID($this->oWebsite->getID())
		;

		return $this->oElementTipGame->getHtmlForForm(false);
	}

}
