<?php

/**
 * @property Player_model $player
 *
 * @author Mâssieur Hunter
 */
class Player extends MY_Controller
{

    public function __construct()
    {
        if(!is_cli()) {
            // echo 'Not allowed';
            die('no');
        }
        parent::__construct();
    }

    public function index() {

        die('coucou');

    }

    public function create($name) {

    	$password = random_string();
    	
        $this->load->model('player_model', 'player');
        $this->player
            ->setName($name)
            ->hashAndSetPassword($password)
            ->create();

        echo "Joueur $name créé avec le mot de passe $password\n";

    }


}
