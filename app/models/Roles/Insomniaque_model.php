<?php

class Insomniaque_model extends Role_model
{

    public function action() {

    }

    private function getRole() {
        $CI = get_instance();

        /** @var Player_model $oPlayer */
        $oPlayer = $CI->oCurrentPlayer;

        return $oPlayer->getRoleName();

    }

}