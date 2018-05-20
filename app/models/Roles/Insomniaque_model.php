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
    public function action(int $gameUid, Player_model $oPlayer): string
    {
        return $this->getCurrentRole($gameUid, $oPlayer);
    }

    /**
     * @param int $gameUid
     * @param Player_model $oPlayer
     * @return string
     */
    private function getCurrentRole(int $gameUid, Player_model $oPlayer): string
    {

        return $oPlayer->getCurrentRoleName($gameUid);

    }

}