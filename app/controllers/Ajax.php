<?php

/**
 * @property Game_model $game
 * @property Player_model $player
 * @property AjaxResponse $ajax
 *
 * @author Mâssieur Hunter
 */
class Ajax extends MY_Controller
{

    public function __construct() {
        parent::__construct();


        $this->lang->load('main');
        $this->load->library('ajaxResponse', NULL, 'ajax');

        header('Content-Type: application/json');
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

        $this->lang->load('player');
        $messageTranslation = $this->lang->line($loginResult['message']);

        if($loginResult['result']){
            $this->ajax->success($messageTranslation);
            $this->ajax->redirect('/', 1500);
        } else {
            $this->ajax->error($messageTranslation);
        }

        return $loginResult['result']
            ? $this->ajax->t()
            : $this->ajax->f($loginResult['message']);


    }

    private function gameCreate() {

        $this->load->model('game_model', 'game');
        $this->game
            ->generateCode()
            ->setMaxPlayers($this->input->post_get('max-players'))
            ->create();

        return $this->t([$this->game->getCode()]);

    }

}