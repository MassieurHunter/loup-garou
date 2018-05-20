<?php

/**
 * @property Game_model $game
 *
 * @author Mâssieur Hunter
 */
class Lang extends CI_Controller
{

    public function index() {

        echo json_encode($this->lang->language);

    }

}
