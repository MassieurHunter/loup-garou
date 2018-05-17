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
         * We check for the ws_auth cookie
         * If it's there we set the ws_auth session
         */
        if ($this->input->cookie('ws_auth')) {
            $this->session->set_userdata('ws_auth', $this->input->cookie('ws_auth'));
        }

        /*
         * We check for the ws_auth session
         * If we have one we try to set the user
         */
        if ($this->session->has_userdata('ws_auth')) {

            /*
             * if the ws_auth is correct we set the correct language from user's infos
             */
            $this->currentPlayer->autoLogin();
        }
    }

    /**
     * Init the user and set the lang from his preferences
     */
    public function initCurrentGame() {
        $this->load->model('game_model', 'currentGame');
        /*
         * We check for the id cookie
         * If it's there we set the id session
         */
        if ($this->input->cookie('gameCode')) {
            $this->session->set_userdata('gameCode', $this->input->cookie('gameCode'));
        }


        if ($this->session->has_userdata('gameCode')) {
            $gameCode = $this->session->userdata('gameCode');
            $this->currentGame->initByCode($gameCode);
        }
    }

    /**
     *
     * @param string $param
     * @param string $value
     * @return \MY_Controller
     */
    public function addUrlParam($param, $value) {
        $this->arrUrlParams[$param] = $value;
        return $this;
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

    /**
     *
     * @param string $name
     * @return mixed
     */
    protected function getFromPostOrSession($name) {
        $postValue = $this->getFromPost($name);
        return (!empty($postValue) ? $postValue : $this->getFromSession($name));
    }

    /**
     *
     * @param string $name
     * @return mixed
     */
    protected function getFromPost($name) {
        //$this->load->helper('form_helper');
        $post = null;
        if ($this->input->post($name, true)) {
            $post = $this->input->post($name, true);
            //set_value($name, '');
            //$this->input->post($name);
        }
        return $post;
    }

    /**
     *
     * @param string $name
     * @return mixed
     */
    protected function getFromSession($name) {
        $value = null;
        if ($this->session->has_userdata($name)) {
            $value = $this->session->userdata($name);
        }
        return $value;
    }

    /*
     *
     * Current User
     *
     */

    /**
     * Get all the params from the url
     *
     * @return array
     */
    protected function getAllUrlParams() {
        if (empty($this->arrUrlParams)) {
            $this->initParamsFromUrl();
        }

        return $this->arrUrlParams;
    }

    /**
     * send the response to the user
     * in a json object
     * or in plain html
     * depending of the "json" post request
     */
    protected function htmlOrJson() {
        if ($this->isDataPost) {
            $response = [
                'html' => $this->response];
            $this->sendJson($response);
        } else {
            echo $this->response;
        }
    }

    /**
     * Send a json response
     * automatically encode the param in json
     * set the header to json
     * echo the json
     *
     * @param array $response
     * @param boolean $withHeader
     */
    public function sendJson($response = [], $withHeader = true) {
        if ($withHeader) {
            header('Content-Type: application/json');
        }

        echo json_encode($response);
    }

    public function displayWithHeaderAndFooter(){

    }

}
