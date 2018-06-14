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
            ->setVar('header', $this->template->saveInVar('inc/header'))
            ->setVar('footer', $this->template->saveInVar('inc/footer'));

        if (!$this->currentPlayer->getPlayerUid() && !preg_match('/login/si', $this->uri->uri_string)) {
            redirect('/game/login');
            die;
        }
    }


    public function index() {

        $this->overallRanking();

    }

    /**
     * Display the list of the groups
     */
    public function overallRanking() {

		ini_set('xdebug.var_display_max_depth', 5);
		ini_set('xdebug.var_display_max_children', 256);
		ini_set('xdebug.var_display_max_data', 1024);
		$globalStats = $this->stats->getOverallRanking();

		var_dump($globalStats);
		die;
    	
//        $this->template->display('stats/global');

    }
    
    /**
     * Display the list of the groups
     */
    public function playerStats() {

        $this->template->display('stats/player');

    }


}
