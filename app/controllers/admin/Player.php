<?php

/**
 * @property Player_model $player
 *
 * @author Mâssieur Hunter
 */
class Player extends MY_Controller
{


    public function index() {

        die('coucou');

    }

    public function create($name, $password) {

        $this->load->model('player_model', 'player');
        $this->player
            ->setName($name)
            ->hashAndSetPassword($password)
            ->create();

        echo "Joueur $name créé\n";

    }


}
