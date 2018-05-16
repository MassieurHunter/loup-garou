<?php

class Insomniaque_model extends Role_model
{

    public function action() {
        return $this->getCurrentRole();
    }

    private function getCurrentRole() {
        $CI = get_instance();

        /** @var Player_model $oPlayer */
        $oPlayer = $CI->oCurrentPlayer;

        return $oPlayer->getCurrentRoleName();

    }

}