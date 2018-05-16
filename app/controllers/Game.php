<?php

/**
 * @property Game_model $game
 * 
 * @author Mâssieur Hunter
 */
class Game extends MY_Controller
{

	/*
	 *
	 *
	 * PUBLIC METHODS
	 * REACHABLES BY URL
	 *
	 *
	 */

	public function index() {

		$this->welcome();

	}

	/**
	 * Display the list of the groups
	 */
	public function welcome() {

		$this->template->display('welcome');

	}


	public function creator(){

	    $this->template->display('game/create');

    }

	public function create(){

        $this->load->model('game_model', 'game');
        $this->game
            ->generateCode()
            ->create()
        ;

        $this->template
            ->setVar('newGameCode', $this->game->getCode())
            ->display('game/create');

    }

    public function join($code){

        $this->load->model('game_model', 'game');
        $this->game->initByCode($code);

    }


}
