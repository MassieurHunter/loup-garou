<?php

/**
 * @property Game_model $game
 * @property Player_model $player
 * @property AjaxResponse $ajax
 * @property CI_Lang $lang
 * @property Statistics_model $stats
 *
 * @author Mâssieur Hunter
 */
class Ajax extends MY_Controller
{

	public function __construct() {
		parent::__construct();

		$this->load->library('ajaxResponse', NULL, 'ajax');

		$this->executeMethod();

	}

	private function executeMethod() {
		$target = $this->input->get_post('target');

		$method = lcfirst(str_replace('/', '', ucwords($target, '/')));

		if (method_exists($this, $method) && is_callable([$this, $method])) {

			$this->ajax->setFormTarget($target);
			$data = $this->{$method}();

		} else {

			$messageTranslation = $this->lang->line('unknown_method');
			$data = $this->ajax->f($messageTranslation);

		}


		header('Content-Type: application/json');
		// override HTTP status code
		http_response_code($data['code']);

		// encode our data array
		echo json_encode($data['body'], JSON_BIGINT_AS_STRING);
		die;
	}

	public function index() {
	}


	/**
	 * @return array
	 */
	private function playerLogin(): array {

		$name = $this->input->post_get('name');
		$password = $this->input->post('password');

		$loginResult = $this->currentPlayer->login($name, $password);

		$messageTranslation = $this->lang->line($loginResult['message']);

		if ($loginResult['success']) {
			$this->ajax->success($messageTranslation);
			$this->ajax->redirect('/', 1500);
		} else {
			$this->ajax->error($messageTranslation);
		}

		return $loginResult['success']
			? $this->ajax->t()
			: $this->ajax->f($loginResult['message']);

	}

	private function playerTheme() {

		$this->currentPlayer
			->setTheme($this->input->post('theme'))
			->saveModifications();

		return $this->ajax->t();
	}

	/**
	 * @return array
	 */
	private function gameFutureRole(): array {

		$this->load->model('game_model', 'game');
		$roles = $this->game
			->setMaxPlayers($this->input->post('nbPlayers'))
			->getRolesNameForCasting();

		return $this->ajax->t(['roles' => $roles]);


	}


	/**
	 * @return array
	 */
	private function gameCreate(): array {

		$this->load->model('game_model', 'game');
		$this->game
			->generateCode()
			->setMaxPlayers($this->input->post('max-players'))
			->create();

		$this->ajax->success($this->lang->line('game_created'));
		$this->ajax->redirect('/game/join/' . $this->game->getCode(), 2000);

		return $this->ajax->t([$this->game->getCode()]);

	}

	/**
	 * @return array
	 */
	private function gameJoin(): array {

		$code = $this->input->post('game-code');
		$this->load->model('game_model', 'game');

		$this->game->initByCode($code);

		$joinResult = $this->currentPlayer->joinGame($this->game);
		$messageTranslation = $this->lang->line($joinResult['message']);

		if ($joinResult['success']) {
			$this->session->set_userdata('gameCode', $code);
			$this->ajax->success($messageTranslation);
			$this->ajax->redirect('/game/play/', 1500);
		} else {
			$this->ajax->error($messageTranslation);
		}

		return $joinResult['success']
			? $this->ajax->t()
			: $this->ajax->f($joinResult['message']);

	}


	/**
	 * @return array
	 */
	private function socketConnection(): array {

		return $this->ajax->t([
			'lang'   => $this->lang->language,
			'game'   => $this->currentGame->getBasicInfos(),
			'player' => $this->currentPlayer->getBasicInfos(),
		]);

	}

	private function gameStart() {
		$this->currentGame->start();

		return $this->ajax->t();

	}


	/**
	 * @return array
	 */
	private function rolesInfos(): array {

		return $this->ajax->t([
			'game' => $this->currentGame->getAdvancedInfos(),
			'role' => $this->currentPlayer->getOriginalRoleWithBasicInfos($this->currentGame->getGameUid()),
		]);

	}


	/**
	 * @return array
	 */
	private function playerActionFirst(): array {

		$arguments = $this->input->post();
		$arguments['currentPlayer'] = $this->currentPlayer;
		$gameUid = $this->currentGame->getGameUid();
		$arguments['gameUid'] = $gameUid;
		$isDoppelFirstAction = $this->currentPlayer->getOriginalRole($gameUid)->getModel() === 'doppelganger'
			&& $this->currentPlayer->getCurrentRole($gameUid)->getModel() === 'doppelganger';
		$isDoppelCopiedRoleFirstAction = isset($arguments['doppel']) && $arguments['doppel'] === '1' && $this->currentPlayer->getOriginalRole($gameUid)->getModel() === 'doppelganger';

		if ($isDoppelCopiedRoleFirstAction) {

			$role = $this->currentPlayer->getCurrentRole($gameUid);

		} else {

			$role = $this->currentPlayer->getOriginalRole($gameUid);

		}

		if (!isset($arguments['nothing'])) {

			$actionResponse = $role->firstAction($arguments);
			$actionMessage = $role->buildActionMessage(1, $actionResponse);


			if ($role->hasSecondAction() || $isDoppelFirstAction) {

				if ($role->isSecondActionNeedFailedFirst()) {

					$socketMessage = $actionResponse['result'] ? 'playerFinishedTurn' : 'playerPlayedFirstAction';


				} else {

					$socketMessage = 'playerPlayedFirstAction';

				}

			} else {

				$socketMessage = 'playerFinishedTurn';

			}


			if ($role->getFirstActionTargetType() !== 'ajax') {

				$this->ajax->socketMessage($socketMessage, [
					'game'    => $this->currentGame->getAdvancedInfos(),
					'player'  => $this->currentPlayer->getBasicInfos(),
					'role'    => $this->currentPlayer->getOriginalRoleWithBasicInfos($gameUid),
					'newRole' => $isDoppelFirstAction ? $this->currentPlayer->getCurrentRoleWithBasicInfos($gameUid) : [],
					'doppel'  => $isDoppelFirstAction,
				]);

			}

		} else {

			$actionMessage = $role->buildActionMessage(0, [
				'currentPlayer' => $this->currentPlayer->getBasicInfos(),
				'gameUid'       => $gameUid,
			]);
			$this->ajax->socketMessage('playerFinishedTurn', [
				'game'    => $this->currentGame->getAdvancedInfos(),
				'player'  => $this->currentPlayer->getBasicInfos(),
				'role'    => $this->currentPlayer->getOriginalRoleWithBasicInfos($gameUid),
				'nothing' => true,
			]);

		}

		$this->ajax->actionResultMessage($actionMessage);

		return $this->ajax->t(
			$role->getFirstActionTargetType() === 'ajax' ? $actionResponse : []
		);

	}

	/**
	 * @return array
	 */
	private function playerActionSecond(): array {

		$arguments = $this->input->post();
		$arguments['currentPlayer'] = $this->currentPlayer;
		$gameUid = $this->currentGame->getGameUid();
		$arguments['gameUid'] = $gameUid;
		$isDoppelCopiedRoleSecondAction = isset($arguments['doppel']) && $arguments['doppel'] === '1' && $this->currentPlayer->getOriginalRole($gameUid)->getModel() === 'doppelganger';

		if ($isDoppelCopiedRoleSecondAction) {

			$role = $this->currentPlayer->getCurrentRole($gameUid);

		} else {

			$role = $this->currentPlayer->getOriginalRole($gameUid);

		}

		$actionResponse = $role->secondAction($arguments);
		$actionMessage = $role->buildActionMessage(2, $actionResponse);

		$this->ajax->socketMessage('playerFinishedTurn', [
			'game'   => $this->currentGame->getAdvancedInfos(),
			'player' => $this->currentPlayer->getBasicInfos(),
			'role'   => $this->currentPlayer->getOriginalRoleWithBasicInfos($gameUid),
		]);

		$this->ajax->actionResultMessage($actionMessage);

		return $this->ajax->t();

	}

	/**
	 * @return array
	 */
	private function playerActionsRebuild(): array {

		$actionsRebuild = $this->currentGame->rebuildActions($this->currentPlayer);

		foreach ($actionsRebuild['actions'] as $actionMessage) {

			$this->ajax->actionResultMessage($actionMessage);

		}

		if ($actionsRebuild['finishedTurn']) {

			$this->ajax->socketMessage('playerFinishedTurn', [
				'game'    => $this->currentGame->getAdvancedInfos(),
				'player'  => $this->currentPlayer->getBasicInfos(),
				'refresh' => true,
			]);


		}

		return $this->ajax->t();

	}

	/**
	 * @return array
	 */
	private function playerVote(): array {
		$this->load->model('player_model', 'player');
		$gameUid = $this->currentGame->getGameUid();
		$playerUid = $this->input->post('playerUid');
		$this->player->init($playerUid);
		$this->currentPlayer->vote($gameUid, $playerUid);

		$message = str_replace('*playername*', $this->player->getName(), $this->lang->line('voted_for'));
		$cancelVote = $this->lang->line('cancel_vote');

		$this->ajax->voteMessage($message, $cancelVote);

		$this->ajax->socketMessage('playerVoted', [
			'game'   => $this->currentGame->getBasicInfos(),
			'player' => $this->currentPlayer->getBasicInfos(),
		]);

		return $this->ajax->t();

	}

	/**
	 * @return array
	 */
	private function playerVoteCancel(): array {
		$gameUid = $this->currentGame->getGameUid();
		$this->currentPlayer->cancelVote($gameUid);

		$this->ajax->socketMessage('playerCanceledVote', [
			'game'   => $this->currentGame->getBasicInfos(),
			'player' => $this->currentPlayer->getBasicInfos(),
		]);

		return $this->ajax->t();

	}

	/**
	 * @return array
	 */
	private function playerVoteRebuild(): array {
		$this->load->model('player_model', 'player');

		$gameUid = $this->currentGame->getGameUid();

		$playerUid = $this->currentPlayer->getVote($gameUid);

		if ($playerUid) {

			$this->player->init($playerUid);
			$message = str_replace('*playername*', $this->player->getName(), $this->lang->line('voted_for'));
			$cancelVote = $this->lang->line('cancel_vote');

			$this->ajax->voteMessage($message, $cancelVote);

			$this->ajax->socketMessage('playerVoted', [
				'game'   => $this->currentGame->getBasicInfos(),
				'player' => $this->currentPlayer->getBasicInfos(),
			]);

		}

		if ($this->currentGame->isFinished()) {

			$this->voteResults();

		}


		return $this->ajax->t();

	}

	/**
	 * @return array
	 */
	private function voteResults(): array {

		$gameResultMessages = $this->currentGame->finish($this->currentPlayer->getPlayerUid());
		$gameSummary = $this->currentGame->getSummary();

		$this->ajax->gameResults($gameResultMessages);
		$this->ajax->gameSummary($gameSummary);

		return $this->ajax->t();

	}

	/**
	 * @return array
	 */
	private function statsOverall(): array {

		$this->load->model('statistics_model', 'stats');
		
		$overallStats = $this->stats->getOverallRanking();

		return $this->ajax->t($overallStats);

	}
	
	/**
	 * @return array
	 */
	private function statsPlayer(): array {

		$this->load->model('statistics_model', 'stats');

		$playerUid = $this->input->post('playerUid');
		$playerStats = $this->stats->getPlayerStats($playerUid);

		return $this->ajax->t($playerStats);

	}

}