<?php

/**
 * @property Game $game
 *
 * @author Mâssieur Hunter
 */
class Game extends MY_Controller
{
    /**
     * Game constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->template
            ->setVar('header', $this->template->saveInVar('inc/header'))
            ->setVar('footer', $this->template->saveInVar('inc/footer'));

        if(!$this->currentPlayer->getPlayerUid()){
            $this->login();
            die;
        }
    }


    public function index() {

        $this->welcome();

    }

    public function login(){
        $this->template->display('login');
    }

    /**
     * Display the list of the groups
     */
    public function welcome() {

        $this->template->display('welcome');

    }


    public function creator() {

        $this->template->display('game/create');

    }

    public function create() {

        $this->load->model('game_model', 'game');
        $this->game
            ->generateCode()
            ->create();

        $this->template
            ->setVar('newGameCode', $this->game->getCode())
            ->display('game/create');

    }

    public function join($code = null) {

        if($code){
            $this->session->set_userdata('gameCode', $code);
        }

        $this->initCurrentGame();

        if($this->currentGame->getGameUid()){
            redirect('/game/play');
        }


    }

    public function play(){

        var_dump($this->currentGame);

    }


}
