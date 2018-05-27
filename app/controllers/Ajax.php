<?php

/**
 * @property Game_model $game
 * @property Player_model $player
 * @property AjaxResponse $ajax
 * @property CI_Lang $lang
 *
 * @author Mâssieur Hunter
 */
class Ajax extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('ajaxResponse', NULL, 'ajax');

        $this->executeMethod();

    }

    public function index()
    {
    }

    private function executeMethod()
    {
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

    private function playerLogin(): array
    {

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

    private function gameCreate(): array
    {

        $this->load->model('game_model', 'game');
        $this->game
            ->generateCode()
            ->setMaxPlayers($this->input->post('max-players'))
            ->create();

        $this->ajax->success($this->lang->line('game_created'));
        $this->ajax->redirect('/game/join/' . $this->game->getCode(), 2000);

        return $this->ajax->t([$this->game->getCode()]);

    }

    private function gameJoin(): array
    {

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

    private function socketConnection(): array
    {

        return $this->ajax->t([
            'lang' => $this->lang->language,
            'game' => $this->currentGame->getBasicInfos(),
            'player' => $this->currentPlayer->getBasicInfos(),
        ]);

    }

    private function gameStart(): array
    {

        if (!$this->currentGame->isStarted()) {
            $this->currentGame->start();
        }

        return $this->ajax->t([
            'game' => $this->currentGame->getAdvancedInfos(),
            'role' => $this->currentPlayer->getOriginalRoleWithBasicInfos($this->currentGame->getGameUid()),
            'firstRole' => $this->currentGame->getFirstRole(),
        ]);

    }

    private function playerActionFirst(): array
    {

        $arguments = $this->input->post();
        $arguments['currentPlayer'] = $this->currentPlayer;
        $arguments['gameUid'] = $this->currentGame->getGameUid();

        $actionResponse = $this->currentPlayer->roleFirstAction($arguments);

        $this->ajax->socketMessage('playerPlayedFirstAction', [
            'game' => $this->currentGame->getBasicInfos(),
            'player' => $this->currentPlayer->getBasicInfos(),
            'role' => $this->currentPlayer->getOriginalRoleModel($this->currentGame->getGameUid()),
        ]);

        return $actionResponse
            ? $this->ajax->t($actionResponse)
            : $this->ajax->f([]);

    }

    private function playerActionSecond(): array
    {

        $arguments = $this->input->post();
        $arguments['currentPlayer'] = $this->currentPlayer;
        $arguments['gameUid'] = $this->currentGame->getGameUid();

        $actionResponse = $this->currentPlayer->roleSecondAction($arguments);
        $originalRoleModel = $this->currentPlayer->getOriginalRoleModel($this->currentGame->getGameUid());

        $this->ajax->socketMessage('playerFinishedTurn', [
            'game' => $this->currentGame->getBasicInfos(),
            'player' => $this->currentPlayer->getBasicInfos(),
            'role' => $this->currentPlayer->getOriginalRoleWithBasicInfos($this->currentGame->getGameUid()),
            'nextRole' => $this->currentGame->getNextRole($originalRoleModel->getModel())
        ]);

        return $actionResponse
            ? $this->ajax->t($actionResponse)
            : $this->ajax->f([]);

    }

    private function gameNextRole(): array
    {
        $originalRoleModel = $this->currentPlayer->getOriginalRoleModel($this->currentGame->getGameUid());

        return $this->ajax->t(
            $this->currentGame->getNextRole($originalRoleModel->getModel())
        );

    }

    private function playerVote(): array
    {
        $this->load->model('player_model', 'player');
        $gameUid = $this->currentGame->getGameUid();
        $targetUid = $this->input->post('targetUid');
        $this->player->init($targetUid);
        $this->currentPlayer->vote($gameUid, $targetUid);

        return $this->ajax->t(
            [
                $this->player->getBasicInfos()
            ]
        );

    }

}