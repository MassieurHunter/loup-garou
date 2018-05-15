<?php

/**
 * Extension OF CI_Controller
 *
 * @author Massieur Hunter
 * @property \Template $template Template engine
 * @property \CI_Input $input
 * @property \Player_model $oCurrentPlayer
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

    public function __construct() {
        $this->microtime = microtime(true);
        parent::__construct();
        $this->load->library('template');
        $this->load->library('dompdf');
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

        /*
         * Get al the availables languages
         */

        $this->initCurrentPlayer();

        $this->template->setVar('svgPath', URL_SVG);
    }

    /**
     * Init the user and set the lang from his preferences
     */
    public function initCurrentPlayer() {
        $this->load->model('user/user_model', 'oCurrentPlayer');
        /*
         * We check for the id cookie
         * If it's there we set the id session
         */
        if ($this->input->cookie('id')) {
            $this->session->set_userdata('id', $this->input->cookie('id'));
        }


        if ($this->session->has_userdata('id')) {
            $playerUid = $this->session->get_userdata('id');
            $this->oCurrentPlayer->init($playerUid);
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

    public function isUserLoggedIn() {
        return $this->oCurrentPlayer->getID() > 0;
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
        $value = '';
        if ($this->input->post($name, true)) {
            $value = $this->input->post($name, true);
            //set_value($name, '');
            //$this->input->post($name);
        }
        return $value;
    }

    /**
     *
     * @param string $name
     * @return mixed
     */
    protected function getFromSession($name) {
        $value = '';
        if ($this->session->has_userdata($name)) {
            $value = $this->session->userdata($name);
        }
        return $value;
    }

    /**
     * Get a url parameter
     *
     * @param string $name
     * @return mixed
     */
    protected function getParamFromUrl($name) {
        if (empty($this->arrUrlParams)) {
            $this->initParamsFromUrl();
        }

        return isset($this->arrUrlParams[$name]) ? $this->arrUrlParams[$name] : NULL;
    }

    /**
     * fill the url params array
     */
    protected function initParamsFromUrl() {
        $arrSegments = $this->uri->segment_array();

        $classNamePassed = false;
        $methodPassed = false;
        $i = 0;
        $lastParameName = '';
        foreach ($arrSegments as $segment) {
            if (!$methodPassed) {
                if (!$classNamePassed) {
                    if (strtolower($segment) == strtolower(get_class($this))) {
                        $classNamePassed = true;
                    }
                } else {
                    if (method_exists($this, $segment)) {
                        $methodPassed = true;
                    }
                }
            } else {
                if ($i % 2 === 0) {
                    $lastParameName = xss_clean($segment);
                } else {
                    $this->arrUrlParams[$lastParameName] = xss_clean($segment);
                }

                $i++;
            }
        }
    }

    /**
     * Get a url parameter
     *
     * @param string $name
     * @return mixed
     */
    protected function getPostOrUrl($name, $xssClean = false) {
        if (empty($this->arrPostUrl)) {
            $this->initPostUrl();
        }

        $return = isset($this->arrPostUrl[$name]) ? $this->arrPostUrl[$name] : NULL;

        if ($xssClean) {
            $return = $this->security->xss_clean($return);
        }

        return $return;
    }

    /**
     * fill the url params array
     */
    protected function initPostUrl() {
        $arrPost = $this->input->post();
        $arrUrl = $this->getAllUrlParams();

        foreach ($arrPost as $key => $value) {
            $this->arrPostUrl[$key] = $value;
        }

        foreach ($arrUrl as $key => $value) {
            if (!isset($this->arrPostUrl[$key])) {
                $this->arrPostUrl[$key] = $value;
            }
        }
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
     * Get all the params from the url
     *
     * @return array
     */
    protected function getAllPostOrGet() {
        if (empty($this->arrPostUrl)) {
            $this->initPostUrl();
        }

        return $this->arrPostUrl;
    }

    /*
     *
     *
     * WHO IS ONLINE
     *
     *
     */

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
     * @param string $response
     * @param boolean $withCSRF
     */
    public function sendJson($response = [], $withCSRF = true, $withHeader = true) {
        if ($withHeader) {
            header('Content-Type: application/json');
        }

        if ($withCSRF) {
            $response['csrfToken'] = [
                'name'  => $this->csrfTokenName,
                'value' => $this->csrfHash,];
        }

        echo json_encode($response);
    }

}
