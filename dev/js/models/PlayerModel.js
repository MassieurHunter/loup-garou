import BaseModel from './BaseModel';
import RoleModel from './RoleModel';
import GameModel from './GameModel';
import LangModel from './LangModel';
import ABuilder from '../tools/ABuilder';
import Ajax from '../tools/Ajax';
import Forms from "../components/Forms";

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
        this.getLangModel().getLine(this.getRoleModel().displayName());
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
        let targets = [];
        let actionChoiceButtons = [];
        let actionForm = $('.play-and-vote-form');
        let typeInput = null;
        let submitButton = new ABuilder(
            'input',
            {
                'class': 'btn btn-primary',
                'type': 'submit',
            },
            this.getLangModel().getLine('submit')
        );
        let roleTitle = new ABuilder(
            'h4',
            {
                'class': 'text-center',
            },
            this.displayRoleName()
        );
        let actionTitle = null;


        if (actionTargetType === 'ajax') {
            Ajax.post('player/action/first', [], (response) => {
                let i = 0;
                for (let action of response.data) {
                    let btnType = i === 0 ? 'primary' : 'secondary';
                    let actionButton = new ABuilder(
                        'button',
                        {
                            'class': 'btn btn-' + btnType,
                            'type': 'button',
                            'data-name': action.name,
                            'data-type': action.type,
                            'data-nbTargets': action.nbTargets,
                            'data-targetType': action.targetType
                        },
                        action.name
                    );

                    actionButton.on('click', () => {
                        Role.setSecondActionName(actionButton.data('name'));
                        Role.setSecondActionNbTargets(actionButton.data('nbTargets'));
                        Role.setSecondActionTargetType(actionButton.data('targetType'));
                        Role.setType(actionButton.data('type'));
                        this.setRole(Role.toJSON());

                        this.displayAction('second')
                    });

                    actionChoiceButtons.push(
                        actionButton
                    );
                    i++;
                }
            });
        } else {

            actionTitle = new ABuilder('h4', {
                'class': 'action-title',
            }, actionName);

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
                playersOptions.push(new ABuilder('option', {
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

                targets.push(emptyInputGroup);
            }

            targets.push(submitButton);

            if (Role.getType()) {
                typeInput = new ABuilder(
                    'input',
                    {
                        'type': 'hidden',
                        'name': 'type',
                        'value': Role.getType()
                    },
                    ''
                )
            }

        }

        actionForm
            .append(actionChoiceButtons)
            .append(typeInput)
            .append(targets)
            .attr({'data-target': 'player/action/' + firstOrSecond});

        let actionContent = new ABuilder(
            'div',
            {
                'class': 'row action-form-container',
            },
            new ABuilder(
                'div',
                {
                    'class': 'col-lg-8 offset-lg-2 col-md-8 offset-md-2 mt-1',
                },
                [
                    roleTitle,
                    actionTitle
                ]
            )
        );

        actionForm.appendTo(actionContent);
        $('.play-game').html(actionContent);

        let forms = new Forms();

        if (actionIsPassive) {
            actionContent.addClass('d-none');
            actionForm.trigger('submit');
        }

    }

    displayRoleAndVote() {
        let Game = this.getGameModel();
        let Lang = this.getLangModel();
        let gamePlayers = Game.getPlayersModel();
        let submitButton = new ABuilder(
            'input',
            {
                'class': 'btn btn-primary',
                'type': 'submit',
            },
            this.getLangModel().getLine('vote')
        );

        let title = new ABuilder('h4', {
            'class': 'action-title',
        }, Lang.getLine('vote_for'));

        /*
         * select for players
         */
        let playersOptions = [];

        for (let player of gamePlayers) {

            if (player.getPlayerUid() !== this.getPlayerUid()) {
                playersOptions.push(new ABuilder('option', {
                    'value': player.getPlayerUid(),
                }, player.getName()))
            }

        }

        let playersSelect = new ABuilder('select', {
            'class': 'custom-select',
        }, playersOptions);

        let actionForm = new ABuilder(
            'form',
            {
                'class': 'ajax-form',
                'data-target': 'player/vote/'
            },
            [
                playersSelect,
                submitButton
            ]
        );

        let actionContent = new ABuilder(
            'div',
            {
                'class': 'row action-form-container',
            },
            new ABuilder(
                'div',
                {
                    'class': 'col-lg-8 offset-lg-2 col-md-8 offset-md-2 mt-1',
                },
                [
                    title,
                    actionForm
                ]
            )
        );

        $('.play-game').html(actionContent);

    }

}