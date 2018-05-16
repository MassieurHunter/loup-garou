<?php

/**
 * Class Insomniaque_model
 */
class Insomniaque_model extends Role_model
{

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @return string
     */
    public function action($gameUid, $oPlayer) {
        return $this->getCurrentRole($gameUid, $oPlayer);
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @return string
     */
    private function getCurrentRole($gameUid, $oPlayer) {

        return $oPlayer->getCurrentRoleName($gameUid);

    }

}