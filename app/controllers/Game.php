<?php

/**
 * @property Game_model $game
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

        $this->lang->load('main');

        $this->template
            ->setVar('langMain', $this->lang->language)
            ->setVar('header', $this->template->saveInVar('inc/header'))
            ->setVar('footer', $this->template->saveInVar('inc/footer'));

        if (!$this->currentPlayer->getPlayerUid() && !preg_match('/login/si', $this->uri->uri_string)) {
            redirect('/game/login');
            die;
        }
    }


    public function index() {

        $this->welcome();

    }

    /**
     * Display the list of the groups
     */
    public function welcome() {

        $this->template->display('welcome');

    }

    public function login() {
        $this->template->display('login');
    }

    public function create() {

        $this->template->display('game/create');

    }

    public function join($code = null) {

        if ($code) {
            $this->session->set_userdata('gameCode', $code);
        }

        $this->initCurrentGame();

        if ($this->currentGame->getGameUid()) {
            redirect('/game/play');
        }



    }

    public function play() {

        var_dump($this->currentGame);

    }


}
