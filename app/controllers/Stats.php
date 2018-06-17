<?php

/**
 * @author Mâssieur Hunter
 * 
 * @property Statistics_model $stats
 */
class Stats extends MY_Controller
{
    /**
     * Game constructor.
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->model('statistics_model', 'stats');

        $this->template
            ->setVar('lang', $this->lang->language)
			->setVar('container', 'container-fluid')
            ->setVar('header', $this->template->saveInVar('inc/header'))
            ->setVar('footer', $this->template->saveInVar('inc/footer'));

        if (!$this->currentPlayer->getPlayerUid() && !preg_match('/login/si', $this->uri->uri_string)) {
            redirect('/game/login');
            die;
        }
    }


    public function index() {

        $this->template->display('stats');

    }


}
