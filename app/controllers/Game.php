<?php

/**
 * Manag the teams, the groups, the users
 *
 * 
 * @author Mâssieur Hunter
 */
class Game extends MY_Controller
{

	/*
	 *
	 *
	 * PUBLIC METHODS
	 * REACHABLES BY URL
	 *
	 *
	 */

	public function index() {

		$this->welcome();

	}

	/**
	 * Display the list of the groups
	 */
	public function welcome() {
		$this->response = $this->template
				->display('welcome');
	}

	/**
	 * Display group/team members
	 */
	public function membersList() {
		$groupID = $this->getPostOrUrl('groupID', true);

		$arrOGroups = $this->oClub->getAllEmailGroups();

		if ($groupID) {
			if (!$this->oClub->ownsGroup($groupID)) {
				$notYourGroup = $this->template->saveInVar('teamManager/notYourGroup');

				$this->response = $this->template
						->setVar('pannelContent', $notYourGroup)
						->setVar('navigationBar', $this->navigationBarForGroupsList())
						->saveInVar('teamManager/index');

				$this->htmlOrJson();
				die;
			}

			$this->oGroup = $arrOGroups[$groupID];
			$this->oGroup->syncExtraInfosForAllMembers();

			if ($this->oGroup->hasTeam()) {
				$this->template
						->setVar('firstClickLeaguesBox', $this->firstClickLeaguesBox())
						->setVar('sportPositions', $this->oGroup->getFirstTeam()->getOSport()->getAllPlayerPositionsWithBasicInfos())
						->setVar('staffPositions', $this->oClub->getArrStaffPositionsWithBasicInfos())
						->setVar('seasons', $this->oGroup->getFirstTeam()->getAllSeasons())
						->setVar('team', $this->oGroup->getFirstTeam()->getBasicInfos())
						->setVar('canViewTeam', $this->oClub->canViewTeam($this->oGroup->getFirstTeam()->getID()))
						->setVar('hasMedicoRights', $this->oClub->hasMedicoRights($this->oGroup->getOClub()->getID()))
						->setVar('nbGrades', count($this->oGroup->getFirstTeam()->getOSport()->getArrOGrades()))
						->setVar('canEditTeam', $this->oClub->canEditTeam($this->oGroup->getFirstTeam()->getID()))
						->setVar('groupSeason', $this->oGroup->getFirstTeamSeason());
			}

			$this->template
					->setVar('pannelContent', $this->membersListPage())
					->setVar('navigationBar', $this->navigationBarForMembersList());

			$this->clearMemory();
			$this->response = $this->template->saveInVar('teamManager/index');

			$this->htmlOrJson();
		}
	}

	/**
	 * Display the result and standings page for a specific group with team
	 */
	public function resultsAndStandings() {
		$groupID	 = $this->getPostOrUrl('groupID', true);
		$leagueID	 = $this->getPostOrUrl('leagueID', true);
		$teamID		 = $this->getPostOrUrl('teamID', true);
		$season		 = $this->getPostOrUrl('season', true);
		/*
		 * If no groups league and season 
		 * we find the most recent season for the main team of the club
		 */
		if ((empty($groupID)) && (empty($leagueID)) && (empty($teamID)) && (empty($season))) {
			$arrOMainTeams = $this->oClub->getArrMainOTeams();
			if (!empty($arrOMainTeams)) {
				$oTeam					 = array_shift($arrOMainTeams);
				$arrTeamGroupsSeasons	 = $oTeam->getAllGroupsSeasons($this->oClub->getID());
				$allLeaguesAndSeasons	 = $oTeam->getOLeaguesAndSeasons();
				$allLeaguesAndSeasons	 = array_shift($allLeaguesAndSeasons);
				if ($allLeaguesAndSeasons['seasons']) {
					$arrSeason	 = array_shift($allLeaguesAndSeasons['seasons']);
					$season		 = str_replace('_', '/', $arrSeason);
					foreach ($arrTeamGroupsSeasons as $_groupID => $groupSeason) {
						if ($season == $groupSeason) {
							$groupID = $_groupID;
							break;
						}
					}

					if (!$groupID && !empty($arrTeamGroupsSeasons)) {
						foreach ($arrTeamGroupsSeasons as $_groupID => $groupSeason) {
							$groupID = $_groupID;
							break;
						}
					}
				} else {
					$groupID = key(array_slice($arrTeamGroupsSeasons, -1, 1, TRUE));
				}
				if ($allLeaguesAndSeasons['league']) {
					$leagueID = $allLeaguesAndSeasons['league']->getLeagueID();
				}

				if ($groupID && $leagueID) {
					$url = $this->oWebsite->getCorrectDomain() . '/teams/teammanager/resultsAndStandings/groupID/' . $groupID . '/leagueID/' . (int) $leagueID . '/';
				} elseif ($groupID) {
					$url = $this->oWebsite->getCorrectDomain() . '/teams/teammanager/resultsAndStandings/groupID/' . $groupID . '/';
				} else {
					$url = $this->oWebsite->getCorrectDomain() . '/teams/teammanager/';
				}
				redirect($url);
			}
		}

		/*
		 * if no group
		 * we find the main team of the club
		 */
		if (!$groupID) {
			if ($teamID) {
				$this->load->model('club/teams_model', 'oTeam');
				$this->oTeam->init($teamID);
				$arrTeamGroupsSeasons = $this->oTeam->getAllGroupsSeasons($this->oClub->getID());
				if ($season) {
					$season = str_replace('_', '/', $season);
					foreach ($arrTeamGroupsSeasons as $_groupID => $groupSeason) {
						if ($season == $groupSeason) {
							$groupID = $_groupID;
							break;
						}
					}

					if (!$groupID && !empty($arrTeamGroupsSeasons)) {
						foreach ($arrTeamGroupsSeasons as $_groupID => $groupSeason) {
							$groupID = $_groupID;
							break;
						}
					}
				} else {
					$groupID = key(array_slice($arrTeamGroupsSeasons, -1, 1, TRUE));
				}

				if ($groupID && $leagueID) {
					$url = $this->oWebsite->getCorrectDomain() . '/teams/teammanager/resultsAndStandings/groupID/' . $groupID . '/leagueID/' . (int) $leagueID . '/';
				} elseif ($groupID) {
					$url = $this->oWebsite->getCorrectDomain() . '/teams/teammanager/resultsAndStandings/groupID/' . $groupID . '/';
				} else {
					$url = $this->oWebsite->getCorrectDomain() . '/teams/teammanager/';
				}
				redirect($url);
			} elseif ($leagueID) {
				if ($season) {
					$url = $this->oWebsite->getCorrectDomain() . '/leagues/leaguemanager/resultsAndStandings/leagueID/' . (int) $leagueID . '/season/' . $season . '/';
				} else {
					$url = $this->oWebsite->getCorrectDomain() . '/leagues/leaguemanager/resultsAndStandings/leagueID/' . (int) $leagueID . '/';
				}

				redirect($url);
			}
		}


		$arrOGroups = $this->oClub->getAllEmailGroups();

		if ($groupID) {
			if (!isset($arrOGroups[$groupID])) {
				$notYourGroup = $this->template->saveInVar('teamManager/notYourGroup');

				$this->response = $this->template
						->setVar('pannelContent', $notYourGroup)
						->setVar('navigationBar', $this->navigationBarForGroupsList())
						->saveInVar('teamManager/index');

				$this->htmlOrJson();
				die;
			}

			$this->oGroup = $arrOGroups[$groupID];

			if ($this->oGroup->hasTeam()) {
				$this->load->model('club/leagues_model', 'oLeague', false, array(
					'pkValue' => $leagueID));
				$leagueTeams = $leagueID ? $this->oLeague->getTeamsWithVeryBasicInfos($this->oGroup->getFirstTeamSeason()) : array();


				$this->template
						->setVar('leagueTeams', $leagueTeams)
						->setVar('leagueID', $leagueID)
						->setVar('leagueInfos', $this->oLeague->getAdvancedInfos())
						->setVar('season', $this->oGroup->getFirstTeamSeason())
						->setVar('isLeagueAdmin', $this->oClub->isLeagueAdmin($leagueID))
						->setVar('canEditLeague', $this->oClub->canEditLeague($leagueID))
						->setVar('firstClickArenasBox', $this->firstClickArenasBox())
						->setVar('noArenaFoundBox', $this->noArenaFoundBox())
						->setVar('firstClickLeaguesBox', $this->firstClickLeaguesBox())
						->setVar('sportPositions', $this->oGroup->getFirstTeam()->getOSport()->getAllPlayerPositionsWithBasicInfos())
						->setVar('seasons', $this->oGroup->getFirstTeam()->getAllSeasons())
						->setVar('team', $this->oGroup->getFirstTeam()->getBasicInfos())
						->setVar('canEditTeam', $this->oClub->canEditTeam($this->oGroup->getFirstTeam()->getID()))
						->setVar('groupSeason', $this->oGroup->getFirstTeamSeason())
						->setVar('pannelContent', $this->resultsAndStandingsPage($leagueID))
						->setVar('navigationBar', $this->navigationBarForResultsAndStandings($leagueID));

				$this->clearMemory();
				$this->response = $this->template->saveInVar('teamManager/index');
			} else {
				die('not a team');
			}
			$this->htmlOrJson();
		} else {
			redirect($this->oWebsite->getCorrectDomain() . '/teams/teammanager/');
		}
	}

	/*
	 *
	 *
	 * PRIVATE METHODS
	 * FOR INTERNAL USAGE ONLY
	 *
	 *
	 */

	/**
	 *
	 * @return text
	 */
	private function groupsListPage() {
		$groups = $this->oClub->getGroupsWithBasicAndTeamInfos();

		return $this->template
						->setVar('groups', $groups)
						->setVar('groupsListContent', $this->groupsListContent())
						->saveInVar('teamManager/inc/page/groupsListPage');
	}

	/**
	 *
	 * @return text
	 */
	private function membersListPage($start = 0) {
		$this->load->model('language/country_model', '_oCountry');

		if ($this->oClub->canViewTeam($this->oGroup->getFirstTeam()->getID()) && !empty($this->oGroup->getFirstTeam()->getID())) {
			$arrMembers	 = $this->oGroup->getAllGroupUsersWithAdvancedInfos();
			$nbMembers	 = count($arrMembers);
		} elseif (empty($this->oGroup->getFirstTeam()->getID())) {
			$arrMembers	 = $this->oGroup->getAllGroupUsersWithAdvancedInfos();
			$nbMembers	 = count($arrMembers);
		} else {
			$arrMembers	 = array();
			$nbMembers	 = 0;
		}

		$this->template
				->setVar('arrLeagues', $this->oClub->getLeaguesWithBasicInfos($this->oGroup->getFirstTeam()->getGame()))
				->setVar('teamSportInfos', $this->oGroup->getFirstTeam()->getOSport()->getBasicInfos())
				->setVar('group', $this->oGroup->getAdvancedInfos())
				->setVar('groupID', $this->oGroup->getID())
				->setVar('hasTeam', $this->oGroup->hasTeam())
				->setVar('teamID', $this->oGroup->getFirstTeam()->getID())
				->setVar('teamSeason', $this->oGroup->getFirstTeamSeason())
				->setVar('sendMessageDialog', $this->sendMessageDialog())
				->setVar('newColumnDialog', $this->newColumnDialog())
				->setVar('renameGroupDialog', $this->renameGroupDialog())
				->setVar('importCSVDialog', $this->importCSVDialog())
				->setVar('addTeamSeasonDialog', $this->addTeamSeasonDialog())
				->setVar('members', $arrMembers)
				->setVar('nbMembers', $nbMembers)
				->setVar('hasFederation', $this->oClub->hasFederation())
				->setVar('membersListContent', $this->membersListContent($start))
				->setVar('currencies', $this->_oCountry->getAllCurrencies());
		if ($this->oGroup->hasTeam()) {
			$this->template
					->setVar('editTeamDialog', $this->editTeamDialog())
					->setVar('editTeamPhotoDialog', $this->editTeamPhotoDialog());
		}
		return $this->template->saveInVar('teamManager/inc/page/membersListPage');
	}

	/**
	 *
	 * @return text
	 */
	private function resultsAndStandingsPage($leagueID) {
		$this->load->model('club/leagues_model', 'oLeague');

		if ($leagueID) {
			$this->oLeague->init($leagueID);
		} else {
			$this->oLeague
					->setLeagueID(0)
					->setGame($this->oClub->getGameID());
		}

		return $this->template
						->setVar('arrLeagues', $this->oClub->getLeaguesWithBasicInfos($this->oGroup->getFirstTeam()->getGame()))
						->setVar('teamSportInfos', $this->oGroup->getFirstTeam()->getOSport()->getBasicInfos())
						->setVar('group', $this->oGroup->getAdvancedInfos())
						->setVar('groupID', $this->oGroup->getID())
						->setVar('hasTeam', $this->oGroup->hasTeam())
						->setVar('canViewTeam', $this->oClub->canViewTeam($this->oGroup->getFirstTeam()->getID()))
						->setVar('teamID', $this->oGroup->getFirstTeam()->getID())
						->setVar('teamSeason', $this->oGroup->getFirstTeamSeason())
						->setVar('season', $this->oGroup->getFirstTeamSeason())
						->setVar('leagueID', $leagueID)
						->setVar('leagueInfos', $this->oLeague->getAdvancedInfos())
						->setVar('sendMessageDialog', $this->sendMessageDialog())
						->setVar('addTeamSeasonDialog', $this->addTeamSeasonDialog())
						->setVar('newColumnDialog', $this->newColumnDialog())
						->setVar('renameGroupDialog', $this->renameGroupDialog())
						->setVar('editTeamDialog', $this->editTeamDialog())
						->setVar('addGameDialog', $this->addGameDialog())
						->setVar('addMultipleGamesDialog', $this->addMultipleGamesDialog())
						->setVar('addLeagueSeasonDialog', $this->addLeagueSeasonDialog())
						->setVar('standingsContent', $this->standingsContent())
						->setVar('scenesContent', $this->scenesContent())
						->setVar('tableResultContent', $this->tableResultContent())
						->setVar('noTeamFoundBox', $this->noTeamFoundBox())
						->setVar('editStandingsDialog', $this->editStandingsDialog())
						->setVar('gameSheetLiveDialog', $this->gameSheetLiveDialog())
						->saveInVar('leagueManager/inc/page/resultsAndStandingsPage');
	}

	private function getAddMedicoDialog() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/addMedicoDialog');
	}

	/**
	 *
	 * @return text
	 */
	private function groupsListContent() {
		return $this->template->saveInVar('teamManager/inc/page/content/groupsListContent');
	}

	/**
	 * 
	 * @return string
	 */
	private function membersListContent($start) {
		return $this->template
						->setVar('membersListHtml', $this->membersListHtml($start))
						->saveInVar('teamManager/inc/page/content/membersListContent');
	}

	/**
	 * 
	 * @return string
	 */
	private function membersListHtml($start) {
		return $this->template
						->setVar('arrGrades', $this->oGroup->getFirstTeam()->getOSport()->getArrGradesWithBasicInfos())
						->setVar('fieldsNotEditable', $this->oGroup->getFieldsNotEditable())
						->setVar('defaultValues', $this->oGroup->getDefaultValues())
						->setVar('hiddenColumns', $this->oGroup->getHiddenColumns())
						->setVar('teamSpecificColumns', $this->oGroup->getTeamSpecificColumns())
						->setVar('sortbableColumns', $this->oGroup->getSortbableColumns())
						->setVar('start', $start)
						->setVar('end', $start + self::MEMBER_DISPLAY_LIMIT)
						->saveInVar('teamManager/inc/page/content/membersListHtml');
	}

	/**
	 * 
	 * @return string
	 */
	private function scenesContent() {
		return $this->template
						->setVar('arrScenes', $this->oGroup->getFirstTeam()->getLeagueScenesWithVeryBasicInfos($this->oLeague->getID(), $this->oGroup->getFirstTeamSeason()))
						->saveInVar('leagueManager/inc/page/content/scenesContent');
	}

	/**
	 * 
	 * @return string
	 */
	private function tableResultContent() {
		return $this->template
						->setVar('arrResults', $this->oLeague->getSeasonResultsTable($this->oGroup->getFirstTeamSeason()))
						->saveInVar('leagueManager/inc/page/content/tableResultsContent');
	}

	/**
	 * 
	 * @return string
	 */
	private function standingsContent() {
		return $this->template
						->setVar('arrEditableTeams', $this->oClub->getEditablesTeamsWithVeryBasicInfos())
						->setVar('arrStandings', $this->oLeague->getStandings($this->oGroup->getFirstTeamSeason()))
						->saveInVar('leagueManager/inc/page/content/standingsContent');
	}

	/**
	 * 
	 * @return string
	 */
	private function groupCreationDialog() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/groupCreationDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function addTeamSeasonDialog() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/addTeamSeasonDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function sendMessageDialog() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/sendMessageDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function renameGroupDialog() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/renameGroupDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function editTeamDialog() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/editTeamDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function editTeamPhotoDialog() {
		return $this->template
						->setVar('teamPhoto', $this->oGroup->getFirstTeam()->getSeasonPhoto($this->oGroup->getFirstTeamSeason()))
						->saveInVar('teamManager/inc/dialogAndAutocomplete/editTeamPhotoDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function importCSVDialog() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/importCSVDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function newColumnDialog() {
		return $this->template->saveInVar('teamManager/inc/dialogAndAutocomplete/newColumnDialog');
	}

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
	private function gameSheetLiveDialog() {
		return $this->template->saveInVar('leagueManager/inc/dialogAndAutocomplete/gameSheetLiveDialog');
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
	private function addLeagueSeasonDialog() {
		return $this->template->saveInVar('leagueManager/inc/dialogAndAutocomplete/addLeagueSeasonDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function addGameDialog() {
		return $this->template
						->setVar('arrGroupsName', $this->oClub->getGroupsDisplayNames())
						->saveInVar('leagueManager/inc/dialogAndAutocomplete/addGameDialog');
	}

	/**
	 * 
	 * @return string
	 */
	private function editStandingsDialog() {
		return $this->template
						->setVar('arrStatsEditables', $this->oLeague->getArrStatsEditables())
						->saveInVar('leagueManager/inc/dialogAndAutocomplete/editStandingsDialog');
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
		$arrAdminLeagues = $this->oClub->getLeaguesAdminWithBasicInfos($this->oClub->getSportID());

		return $this->template
						->setVar('arrAdminLeagues', $arrAdminLeagues)
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
						->setVar('arrClubTeams', array_slice($arrClubTeams, 0, 30, true))
						->setVar('arrOpponents', array_slice($arrOpponents, 0, 30, true))
						->saveInVar('teamManager/inc/dialogAndAutocomplete/firstClickTeamsBox');
	}

	/**
	 * 
	 * @return string
	 */
	private function firstClickArenasBox() {
		$arrArenas = $this->oClub->getUsedArenasWithBasicInfos($this->oGroup->getFirstTeamSeason());

		return $this->template
						->setVar('arrArenas', $arrArenas)
						->saveInVar('leagueManager/inc/dialogAndAutocomplete/firstClickArenasBox');
	}

	/*
	 *
	 *
	 *
	 * NAVIGATION BAR
	 *
	 *
	 *
	 */

	/**
	 * Generates the navigation bar for the "all plannings" pages
	 *
	 * @return string
	 */
	private function navigationBarForGroupsList() {
		$arrInfos			 = array();
		$arrInfos['h1']		 = $this->translationsTeamManagement->getTranslation('group_overview');
		$arrInfos['h1Link']	 = array(
			'activated'	 => true,
			'title'		 => $this->translationsTeamManagement->getTranslation('group_overview'),
			'href'		 => 'teams/teammanager/',
		);
		$arrInfos['topMenu'] = array();

		$arrInfos['navigationMenu']		 = array();
		$arrInfos['navigationMenu'][0]	 = array(
			'link'	 => 'manager/dashboard/apps/group/' . $this->oCurrentApp->getAppGroupID(),
			'title'	 => $this->oCurrentApp->getOAppGroup()->getNameTranslated(),
		);
		
		$arrInfos['navigationMenu'][1]	 = array(
			'link'	 => false,
			'title'	 => $this->translationsTeamManagement->getTranslation('group_overview'),
		);

		/*
		 * groups dropdown list
		 */
		$arrGroups				 = $this->oClub->getGroupsWithBasicAndTeamInfos();
		$arrInfos['topMenu'][0]	 = array(
			'isDropDown'			 => true,
			'isLink'				 => false,
			'dropdownClass'			 => 'groupSelector',
			'hasAddElementButton'	 => true,
			'addElementButton'		 => array(
				'name'	 => 'addGroupMenuButton',
				'text'	 => $this->translationsTeamManagement->getTranslation('tab_index_add_group'),
			),
			'dropDownSelectedOption' => array(
				'text' => $this->translationsTeamManagement->getTranslation('chooseGroup'),
			),
		);

		foreach ($arrGroups as $groupID => $group) {
			$groupName = isset($group['infoTeam']) && !empty($group['infoTeam']) ? $group['infoTeam']['name'] . ' - ' . $group['infoTeam']['subname'] . ' ( ' . $group['infoTeam']['agegroup'] . ' ' . $group['infoTeam']['genderLetter'] . ' ' . ' )' : $group['infoGroup']['name'];

			$arrInfos['topMenu'][0]['dropDownOption'][$groupID] = array(
				'id'		 => $groupID,
				'datas'		 => array(
					'groupid' => $group['infoGroup']['groupID']),
				'text'		 => $groupName,
				'selected'	 => false,
			);
		}
		$arrInfos['breadCrum'] = array();

		/*
		 * Generate the menu and returns it
		 */
		return $this->navigationBar($arrInfos);
	}

	/**
	 * Generates the navigation bar for the "all plannings" pages
	 *
	 * @return string
	 */
	private function navigationBarForMembersList() {
		$arrInfos			 = array();
		$arrInfos['h1']		 = $this->translationsTeamManagement->getTranslation('group_overview');
		$arrInfos['h1Link']	 = array(
			'activated'	 => true,
			'title'		 => $this->translationsTeamManagement->getTranslation('group_overview'),
			'href'		 => 'teams/teammanager/',
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
			'link'	 => 'teams/teammanager',
			'title'	 => $this->translationsTeamManagement->getTranslation('group_overview'),
		);

		$arrInfos['navigationMenu'][2] = array(
			'link'	 => false,
			'title'	 => $this->oGroup->getName(),
		);
		/*
		 * Generate the top menu
		 */

		/*
		 * groups dropdown list
		 */
		$arrInfos['topMenu']	 = array();
		$arrInfos['topMenu'][0]	 = array(
			'isDropDown'			 => true,
			'isLink'				 => false,
			'dropdownClass'			 => 'groupSelector',
			'image'					 => $this->oGroup->hasTeam() ? $this->oGroup->getFirstTeam()->getLogo() : false,
			'hasAddElementButton'	 => true,
			'addElementButton'		 => array(
				'name'	 => 'addGroupMenuButton',
				'text'	 => $this->translationsTeamManagement->getTranslation('tab_index_add_group'),
			),
			'dropDownSelectedOption' => array(
				'text' => $this->oGroup->hasTeam() ? $this->oGroup->getFirstTeam()->getName() . '-' . $this->oGroup->getFirstTeam()->getSubname() . ' ( ' . $this->oGroup->getFirstTeam()->getAgegroupFormat() . ' ' . $this->oGroup->getFirstTeam()->getGenderLetter() . ' ) ' : $this->oGroup->getName(),
			),
		);

		foreach ($this->oClub->getGroupsWithBasicAndTeamInfos() as $groupID => $group) {
			$currentGroupTestID	 = $this->oGroup->hasTeam() ? $this->oGroup->getFirstTeam()->getID() : $this->oGroup->getID();
			$testID				 = isset($group['infoTeam']) && !empty($group['infoTeam']) ? $group['infoTeam']['teamID'] : $group['infoGroup']['groupID'];
			$groupName			 = isset($group['infoTeam']) && !empty($group['infoTeam']) ? $group['infoTeam']['name'] . ' - ' . $group['infoTeam']['subname'] . ' ( ' . $group['infoTeam']['agegroup'] . ' ' . $group['infoTeam']['genderLetter'] . ' ' . ' )' : $group['infoGroup']['name'];

			$arrInfos['topMenu'][0]['dropDownOption'][$groupID] = array(
				'id'		 => $groupID,
				'datas'		 => array(
					'groupid' => $group['infoGroup']['groupID']),
				'text'		 => $groupName,
				'selected'	 => $currentGroupTestID == $testID,
			);
		}

		if ($this->oGroup->hasTeam() && $this->oClub->canViewTeam($this->oGroup->getFirstTeam()->getID())) {

			$arrSeasons = $this->oGroup->getFirstTeam()->getAllGroupsSeasons($this->oClub->getID());
			arsort($arrSeasons);

			/*
			 * League & Seaons dropdown list
			 */
			$arrInfos['topMenu'][1] = array(
				'isDropDown'			 => true,
				'isLink'				 => false,
				'dropdownClass'			 => 'leagueSeasonSelector',
				'hasAddElementButton'	 => true,
				'addElementButton'		 => array(
					'name'	 => 'addTeamSeasonMenuButton',
					'text'	 => $this->translationsTeamManagement->getTranslation('new_season'),
				),
				'dropDownSelectedOption' => array(
					'text' => $this->oGroup->getFirstTeamSeason() ? $this->oGroup->getFirstTeamSeason() : '&nbsp;',
				),
			);

			foreach ($arrSeasons as $seasonGroupID => $season) {
				$arrInfos['topMenu'][1]['dropDownOption'][$season] = array(
					'id'		 => $season,
					'datas'		 => array(
						'groupid' => $seasonGroupID,
					),
					'text'		 => $season,
					'selected'	 => $season == $this->oGroup->getFirstTeamSeason(),
				);
			}
		}

		$arrInfos['breadCrum'] = array();
		/*
		 * Generate the breadcrum
		 */

		/*
		 * Send a message
		 */
		$arrInfos['breadCrum'][] = array(
			'classes'			 => array(
				'sent',
				'sendMessage'),
			'icon'				 => 'sent',
			'hasNotification'	 => false,
			'text'				 => $this->translationsTeamManagement->getTranslation('new_message'),
		);

		$arrInfos['breadCrum'][] = array(
			'classes'			 => array(
				'list',
				'membersManagement',
				'selected',
			),
			'icon'				 => 'group',
			'hasNotification'	 => false,
			'text'				 => $this->translationsTeamManagement->getTranslation('membersManagement'),
		);

		/*
		 * Results and standings
		 */
		if ($this->oGroup->hasTeam() && $this->oGroup->getFirstTeamSeason()) {
			$arrLeagues	 = $this->oGroup->getFirstTeam()->getOLeaguesForSeason($this->oGroup->getFirstTeamSeason());
			$oLeague	 = array_shift($arrLeagues);

			$arrInfos['breadCrum'][] = array(
				'classes'			 => array(
					'gamesheet',
					'resultsAndStandings',
				),
				'icon'				 => 'gamesheet',
				'hasNotification'	 => false,
				'isLink'			 => true,
				'link'				 => array(
					'href'	 => 'teams/teammanager/resultsAndStandings/groupID/' . $this->oGroup->getID() . (!empty($oLeague) ? '/leagueID/' . $oLeague->getID() . '/' : '/'),
					'title'	 => $this->translationsTeamManagement->getTranslation('Standings_results'),
				),
			);
		}

		/*
		 * Edit team/group
		 */
		if (!$this->oGroup->hasTeam()) {
			$arrInfos['breadCrum'][] = array(
				'classes'	 => array(
					'configuration',
					'changeGroupName',
				),
				'icon'		 => 'settings',
				'text'		 => $this->translationsTeamManagement->getTranslation('configuration'),
			);
		} elseif ($this->oClub->canEditTeam($this->oGroup->getFirstTeam()->getID())) {
			$arrInfos['breadCrum'][] = array(
				'classes'	 => array(
					'configuration',
					'editTeamInNavigation',
				),
				'icon'		 => 'settings',
				'text'		 => $this->translationsTeamManagement->getTranslation('configuration'),
			);
		}

		/*
		 * Generate the menu and returns it
		 */
		return $this->navigationBar($arrInfos);
	}

	/**
	 * Generates the navigation bar for the "all plannings" pages
	 *
	 * @return string
	 */
	private function navigationBarForResultsAndStandings($leagueID) {
		$this->load->model('club/leagues_model', 'oCurrentLeague', false, array(
			'pkValue' => $leagueID));

		$arrInfos			 = array();
		$arrInfos['h1']		 = $this->translationsTeamManagement->getTranslation('group_overview');
		$arrInfos['h1Link']	 = array(
			'activated'	 => true,
			'title'		 => $this->translationsTeamManagement->getTranslation('group_overview'),
			'href'		 => 'teams/teammanager/',
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
			'link'	 => 'teams/teammanager',
			'title'	 => $this->translationsTeamManagement->getTranslation('group_overview'),
		);
		
		$arrInfos['navigationMenu'][2]	 = array(
			'link'	 => false,
			'title'	 => $this->oGroup->getName()
		);
		
		//

		/*
		 * Generate the top menu
		 */

		/*
		 * groups dropdown list
		 */
		$arrOTeams				 = $this->oClub->getArrAllOTeams();
		$arrLeaguesAndSeasons	 = array();

		foreach ($arrOTeams as $teamID => $oTeam) {
			$arrLeaguesAndSeasons[$teamID] = $oTeam->getLeaguesAndSeasonsWithBasicInfos();
			foreach ($arrLeaguesAndSeasons[$teamID] as $_leagueID => $leagueSeason) {
				arsort($arrLeaguesAndSeasons[$teamID][$_leagueID]['seasons']);
			}
		}

		$arrInfos['topMenu']	 = array();
		$arrInfos['topMenu'][0]	 = array(
			'isDropDown'			 => true,
			'isLink'				 => false,
			'dropdownClass'			 => 'groupResultsAndStandingsSelector',
			'hasAddElementButton'	 => true,
			'addElementButton'		 => array(
				'name'	 => 'addGroupMenuButton',
				'text'	 => $this->translationsTeamManagement->getTranslation('tab_index_add_group'),
			),
			'dropDownSelectedOption' => array(
				'text' => $this->oGroup->getFirstTeam()->getName() . '-' . $this->oGroup->getFirstTeam()->getSubname() . ' ( ' . $this->oGroup->getFirstTeam()->getAgegroupFormat() . ' ' . $this->oGroup->getFirstTeam()->getGenderLetter() . ' ) ',
			),
		);

		$currentGroupTestID = $this->oGroup->getFirstTeam()->getID();
		foreach ($this->oClub->getGroupsWithBasicAndTeamInfos() as $groupID => $group) {
			if (isset($group['infoTeam']) && !empty($group['infoTeam'])) {
				$teamID		 = $group['infoTeam']['teamID'];
				$teamName	 = $group['infoTeam']['name'] . ' - ' . $group['infoTeam']['subname'] . ' ( ' . $group['infoTeam']['agegroup'] . ' ' . $group['infoTeam']['genderLetter'] . ' ' . ' )';

				if (isset($arrLeaguesAndSeasons[$teamID])) {
					$leaguesAndSeasons	 = $arrLeaguesAndSeasons[$teamID];
					$firstLeagueID		 = false;
					foreach ($leaguesAndSeasons as $_leagueID => $leagueSeason) {
						if (isset($leagueSeason['seasons'][$group['teamSeason']])) {
							$firstLeagueID = $_leagueID;
							break;
						}
					}

					$arrInfos['topMenu'][0]['dropDownOption'][$groupID] = array(
						'id'		 => $groupID,
						'datas'		 => array(
							'groupid'	 => $group['infoGroup']['groupID'],
							'leagueid'	 => $firstLeagueID,
						),
						'text'		 => $teamName,
						'selected'	 => $currentGroupTestID == $teamID,
					);
				}
			}
		}

		$arrSeasons			 = $this->oGroup->getFirstTeam()->getAllGroupsSeasons($this->oClub->getID());
		$arrSeasonsFliped	 = array_flip($arrSeasons);
		if (isset($arrLeaguesAndSeasons[$this->oGroup->getFirstTeam()->getID()])) {
			$currentTeamLeaguesAndSeasons = $arrLeaguesAndSeasons[$this->oGroup->getFirstTeam()->getID()];
		} else {
			$currentTeamLeaguesAndSeasons = array();
		}

		if ($this->oClub->canViewTeam($this->oGroup->getFirstTeam()->getID())) {
			/*
			 * League & Seaons dropdown list
			 */
			$arrInfos['topMenu'][1] = array(
				'isDropDown'			 => true,
				'isLink'				 => false,
				'dropdownClass'			 => 'groupLeagueSeasonResultsAndStandingsSelector',
				'hasAddElementButton'	 => true,
				'addElementButton'		 => array(
					'name'	 => 'addTeamSeasonMenuButton',
					'text'	 => $this->translationsTeamManagement->getTranslation('new_season'),
				),
				'dropDownSelectedOption' => array(
					'text' => ($this->oCurrentLeague->getID() ? $this->oCurrentLeague->getName() : $this->translationsCms_results->getTranslation('friendlygames')) . ' ' . $this->oGroup->getFirstTeamSeason(),
				),
			);

			$arrLeaguesSeasonSorted	 = array();
			$arrSeasonsToSort		 = array();

			foreach ($currentTeamLeaguesAndSeasons as $_leagueID => $leagueSeason) {
				foreach ($leagueSeason['seasons'] as $season) {
					$arrSeasonsToSort[]			 = $season;
					$arrLeaguesSeasonSorted[]	 = array(
						'name'		 => $leagueSeason['infos']['name'] . ' ' . $season,
						'leagueID'	 => $_leagueID,
						'groupID'	 => $arrSeasonsFliped[$season],
						'season'	 => $season,
						'selected'	 => $season == $this->oGroup->getFirstTeamSeason() && $leagueID == $_leagueID,
					);
				}
			}

			array_multisort($arrSeasonsToSort, SORT_DESC, $arrLeaguesSeasonSorted);

			foreach ($arrLeaguesSeasonSorted as $league) {
				$arrInfos['topMenu'][1]['dropDownOption'][] = array(
					'id'		 => $league['season'],
					'datas'		 => array(
						'groupid'	 => $league['groupID'],
						'leagueid'	 => $league['leagueID'],
					),
					'text'		 => $league['name'],
					'selected'	 => $league['selected'],
				);
			}
		}

		$arrInfos['breadCrum']	 = array();
		/*
		 * Generate the breadcrum
		 */
		$arrInfos['breadCrum'][] = array(
			'classes'			 => array(
				'sent',
				'sendMessage'),
			'icon'				 => 'sent',
			'hasNotification'	 => false,
			'text'				 => $this->translationsTeamManagement->getTranslation('new_message'),
		);

		/*
		 * Results and standings
		 */
		$arrInfos['breadCrum'][] = array(
			'classes'			 => array(
				'list',
				'membersManagement',
			),
			'icon'				 => 'group',
			'hasNotification'	 => false,
			'isLink'			 => true,
			'link'				 => array(
				'href'	 => 'teams/teammanager/memberslist/groupID/' . $this->oGroup->getID() . '/',
				'title'	 => $this->translationsTeamManagement->getTranslation('membersManagement'),
			),
		);

		$arrInfos['breadCrum'][] = array(
			'classes'			 => array(
				'gamesheet',
				'resultsAndStandings',
				'selected',
			),
			'icon'				 => 'gamesheet',
			'hasNotification'	 => false,
			'text'				 => $this->translationsTeamManagement->getTranslation('Standings_results'),
		);

		/*
		 * Edit team/group
		 */
		if (!$this->oGroup->hasTeam()) {
			$arrInfos['breadCrum'][] = array(
				'classes'	 => array(
					'configuration',
					'changeGroupName',
				),
				'icon'		 => 'settings',
				'text'		 => $this->translationsTeamManagement->getTranslation('configuration'),
			);
		} elseif ($this->oClub->canEditTeam($this->oGroup->getFirstTeam()->getID())) {
			$arrInfos['breadCrum'][] = array(
				'classes'	 => array(
					'configuration',
					'editTeamInNavigation',
				),
				'icon'		 => 'settings',
				'text'		 => $this->translationsTeamManagement->getTranslation('configuration'),
			);
		}

		/*
		 * Generate the menu and returns it
		 */
		return $this->navigationBar($arrInfos);
	}

	/*
	 * 
	 * 
	 *
	 * SYNC WITH V1 
	 * 
	 * 
	 * 
	 */

	/**
	 * Create a group for each teams of the current club
	 */
	protected function createGroupsForTeams() {
		$arrOTeams	 = $this->oClub->getArrAllOTeams();
		$arrOGroups	 = $this->oClub->getAllEmailGroups();
		$this->load->model('user/emailgroup_model', 'oGroup');

		foreach ($arrOTeams as $teamID => $oTeam) {
			foreach ($oTeam->getAllSeasons() as $season) {
				$addTeamSeasonToGroup = true;

				foreach ($arrOGroups as $groupID => $oGroup) {
					if ($oGroup->getFirstTeam()->getID() == $teamID && $oGroup->getFirstTeamSeason() == $season) {
						$addTeamSeasonToGroup = false;
						break;
					}
				}

				if ($addTeamSeasonToGroup) {
					$oNewGroup = clone $this->oGroup;
					$oNewGroup
							->setName($oTeam->getName())
							->setDescr('')
							->setPClubID($this->oClub->getID())
							->create();
					$oNewGroup->addTeam($oTeam->getID(), $season);
				}
			}
		}

		$this->oClub->initEmailGroups();
	}

	/**
	 * Give the leagues rights to the current club
	 */
	protected function giveLeaguesRights() {
		$arrOTeams = $this->oClub->getArrAllOTeams();
		foreach ($arrOTeams as $oTeam) {
			foreach ($oTeam->getOLeaguesAndSeasons() as $leagueID => $leagueSeason) {
				$this->oClub->addPermissionForLeague($leagueID, 1);
			}
		}
	}

	/**
	 * give the leagues rights to all the clubs in the database
	 */
	protected function giveAllLeaguesRights() {
		$this->load->model('club/Clubmanager_model', 'oClubManager');
		$arrOClubs = $this->oClubManager->getArrOClubs();
		foreach ($arrOClubs as $oClub) {
			$arrOTeams = $oClub->getArrAllOTeams();
			foreach ($arrOTeams as $oTeam) {
				foreach ($oTeam->getOLeaguesAndSeasons() as $leagueID => $leagueSeason) {
					$oClub->addPermissionForLeague($leagueID, 1);
				}
			}
		}
	}

	private function clearMemory() {
		$this->oCurrentUser						 = null;
		$this->oWebsite							 = null;
		$this->oClub							 = null;
		$this->oTeam							 = null;
		$this->oGroup							 = null;
		$this->oLeague							 = null;
		$this->translationsTeamManagement		 = null;
		$this->translationsLeagueManagement		 = null;
		$this->translationsCms_register			 = null;
		$this->translationsCms_adm_mngemgrps	 = null;
		$this->translationsCms_adm_mngroster	 = null;
		$this->translationsCms_scene			 = null;
		$this->translationsCms_adm_mnggames		 = null;
		$this->translationsCms_adm_onboarding	 = null;
		$this->translationsCms_results			 = null;
		$this->translationsCms_calendar			 = null;
		$this->translationsLicense				 = null;
		$GLOBALS['GEOIP_REGION_NAME']			 = null;
	}

	/*
	 * One shot function done on the team manager lauch
	 * do not use
	  private function convertExtraInfos() {
	  $arrUpdates	 = array();
	  $arrInfos	 = $this->db->select('*')->get('clubs_group_members_extrainfos')->result();

	  foreach ($arrInfos as $info) {
	  if (strpos($info->extraInfos, 'a') === 0) {
	  $arrUpdates[] = array(
	  'extrainfosID'	 => $info->extrainfosID,
	  'extraInfos'	 => json_encode(unserialize($info->extraInfos)),
	  );
	  }
	  }

	  $this->db->update_batch('clubs_group_members_extrainfos', $arrUpdates, 'extrainfosID');
	  }
	 */
}
