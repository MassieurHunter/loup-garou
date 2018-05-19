<?php

/**
 * Extension OF CI_Controller
 *
 * @author Massieur Hunter
 * @property \Template $template Template engine
 * @property \CI_Input $input
 * @property \Game_model $currentGame
 * @property \Player_model $currentPlayer
 * @property \CI_Session $session Session library
 */
class MY_Controller extends CI_Controller
{

    /**
     * CSRF token name
     * @var string
     */
    protected $csrfTokenName;

    /**
     * CSRF hash
     * @var string
     */
    protected $csrfHash;

    /**
     * Contain the params sent in the url
     *
     * @var array
     */
    protected $arrUrlParams = [];

    /**
     * Contain the params sent in post and get
     * only search in get if post is empty
     *
     * @var array
     */
    protected $arrPostUrl = [];

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
     * @var float
     */
    protected $microtime;

    /**
     *
     * @var float
     */
    protected $lastMicrotime;

    /**
     * MY_Controller constructor.
     */
    public function __construct() {
        $this->microtime = microtime(true);
        parent::__construct();
        $this->load->library('template');
        $this->load->library('session');
        $this->load->helper(
            [
                'security',
                'url',
                'string',
                'cookie',
                'text',
                'array',
                'date',
            ]
        );

        $this->initCurrentPlayer();
        $this->initCurrentGame();

    }

    /**
     * Init the user and set the lang from his preferences
     */
    public function initCurrentPlayer() {
        $this->load->model('player_model', 'currentPlayer');
        /*
         * We check for the autoLog cookie
         * If it's there we set the autoLog session
         */
        if ($this->input->cookie('autoLog')) {
            $this->session->set_userdata('autoLog', $this->input->cookie('autoLog'));
        }

        /*
         * We check for the autoLog session
         * If we have one we try to set the user
         */
        if ($this->session->has_userdata('autoLog')) {

            /*
             * if the autoLog is correct we set the correct language from user's infos
             */
            $this->currentPlayer->autoLogin();
        }
    }

    /**
     * Init the user and set the lang from his preferences
     */
    public function initCurrentGame() {
        $this->load->model('game_model', 'currentGame');

        if ($this->session->has_userdata('gameCode')) {
            $gameCode = $this->session->userdata('gameCode');
            $this->currentGame->initByCode($gameCode);
        }
    }

    /**
     * Return execustion time from contruction of the controller
     *
     * @return string
     */
    public function microtimePassed($last = false) {
        $microtimeTest = $last ? $this->lastMicrotime : $this->microtime;
        $microtimePassed = microtime(true) - $microtimeTest;
        $this->lastMicrotime = microtime(true);
        return number_format($microtimePassed, 6, '.', ' ') . ' seconds';
    }


}
