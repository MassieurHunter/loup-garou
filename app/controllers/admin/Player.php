<?php

/**
 * @property Player $game
 *
 * @author Mâssieur Hunter
 */
class Player extends MY_Controller
{


    public function index() {

        die('coucou');

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


}
