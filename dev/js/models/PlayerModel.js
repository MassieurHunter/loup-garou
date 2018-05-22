import BaseModel from './BaseModel';
import RoleModel from './RoleModel';
import GameModel from './GameModel';
import LangModel from './LangModel';
import ABuilder from '../tools/ABuilder';
import Ajax from '../tools/Ajax';

export default class PlayerModel extends BaseModel {

    getPlayerUid() {
        return this.getInt('playerUid');
    }

    getName() {
        return this.get('name');
    }

    getRoleModel() {
        return new RoleModel(this.get('role', {}));
    }

    setRole(role) {
        this.set('role', role);
    }

    getGameModel() {
        return new GameModel(this.get('game', {}));
    }

    setGame(game) {
        this.set('game', game);
    }

    setLang(lang) {
        this.set('lang', lang);
    }

    getLangModel() {
        return new LangModel(this.get('lang', {}));
    }

    displayRoleName() {
        this.getRoleModel().displayName();
    }

    displayAction(firstOrSecond) {

        let Role = this.getRoleModel();
        let Game = this.getGameModel();
        let Lang = this.getLangModel();
        let gamePlayers = Game.getPlayersModel();
        let actionName = firstOrSecond === 'first'
            ? Role.getFirstActionName()
            : Role.getSecondActionName();
        let actionNbTargets = firstOrSecond === 'first'
            ? Role.getFirstActionNbTargets()
            : Role.getSecondActionNbTargets();
        let actionTargetType = firstOrSecond === 'first'
            ? Role.getFirstActionTargetType()
            : Role.getSecondActionTargetType();
        let actionIsPassive = firstOrSecond === 'first'
            ? Role.isFirstActionPassive()
            : Role.isSecondActionPassive();
        let actionContent = [];



        if(actionTargetType === 'ajax'){
            Ajax.post('player/action/first', [], (response) => {
                for(let i = 0;i<response.data.length;i++){

                }
            });
        } else {

            /*
             * select for cards
             */
            let cardsOptions = [];

            for (let i = 1; i < 4; i++) {
                cardsOptions.push(new ABuilder('option', {
                    'value': i,
                }, i))
            }

            let cardsSelect = new ABuilder('select', {
                'class': 'custom-select',
            }, cardsOptions);


            /*
             * select for players
             */
            let playersOptions = [];

            for (let player of gamePlayers) {
                cardsOptions.push(new ABuilder('option', {
                    'value': player.getPlayerUid(),
                }, player.getName()))
            }

            let playersSelect = new ABuilder('select', {
                'class': 'custom-select',
            }, playersOptions);

            /*
             * bootstrap input group
             */
            let inputGroup = new ABuilder(
                'div',
                {
                    'class': 'input-group'
                },
                new ABuilder('div',
                    {
                        'class': 'input-group-prepend'
                    },
                    new ABuilder(
                        'label',
                        {
                            'class': 'input-group-text'
                        },
                        ''
                    )
                )
            );

            /*
             * generate targets
             */
            let targets = [];
            for (let i = 0; i < actionNbTargets; i++) {
                let j = i + 1;

                let emptyInputGroup = inputGroup.clone();

                switch (actionTargetType) {

                    case 'player':

                        emptyInputGroup
                            .append(
                                playersSelect
                                    .attr('id', firstOrSecond + 'ActionPlayer' + j)
                                    .attr('name', 'player' + j)
                            )
                            .find('label').html(Lang.getLine('player_' + j));

                        break;
                    case 'card':

                        emptyInputGroup
                            .append(
                                cardsSelect
                                    .attr('id', firstOrSecond + 'ActionCard' + j)
                                    .attr('name', 'card' + j)
                            )
                            .find('label').html(Lang.getLine('card_' + j));

                        break;

                }
            }


            if (Role.actionIsPassive()) {


            }

        }



        let actionTitle = new ABuilder('h4', {
            'class': 'action-title',
        }, actionName);

    }

}