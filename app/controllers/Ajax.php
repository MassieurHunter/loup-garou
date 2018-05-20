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

    public function __construct() {
        parent::__construct();

        $this->load->library('ajaxResponse', NULL, 'ajax');

        $this->executeMethod();

    }

    public function index(){
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

    private function playerLogin() {

        $name = $this->input->post_get('name');
        $password = $this->input->post_get('password');

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

    private function gameCreate() {

        $this->load->model('game_model', 'game');
        $this->game
            ->generateCode()
            ->setMaxPlayers($this->input->post_get('max-players'))
            ->create();

        $this->ajax->success($this->lang->line('game_created'));
        $this->ajax->redirect('/game/join/' .$this->game->getCode(), 2000);

        return $this->ajax->t([$this->game->getCode()]);

    }

    private function gameJoin()
    {

        $code = $this->input->post_get('game-code');
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

    private function socketConnection()
    {

        $socketUid = $this->input->post_get('socketUid');
        $this->currentPlayer->setSocketUid($this->currentGame, $socketUid);

        $socketClient = new SocketIO('localhost', 3000);

        $socketClient->emit('playerConnected', [
            'socketUid' => $socketUid,
            'nbPlayers' => $this->currentGame->getNbPlayers(),
        ]);

        $this->ajax->t();

    }

}