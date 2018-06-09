import BaseModel from './BaseModel';
import RoleModel from './RoleModel';
import GameModel from './GameModel';
import ABuilder from '../tools/ABuilder';
import Ajax from '../tools/Ajax';
import Forms from "../components/Forms";
import LangModel from "./LangModel";

export default class PlayerModel extends BaseModel {


	setLang(lang) {
		this.set('lang', lang);
	}

	getLangModel() {
		return new LangModel(this.get('lang', {}));
	}

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

	getNewRoleModel() {
		return new RoleModel(this.get('newRole', {}));
	}

	setNewRole(role) {
		this.set('newRole', role);
	}

	getGameModel() {
		return new GameModel(this.get('game', {}));
	}

	setGame(game) {
		this.set('game', game);
	}

	displayRoleName() {
		this.getRoleModel().displayName();
	}

	displayAction(firstOrSecond, newRole = false) {
		
		console.log('display role action', firstOrSecond)

		let doppel = newRole && this.getRoleModel().getModel() === 'doppelganger';
		let Role = newRole ? this.getNewRoleModel() : this.getRoleModel();
		let Game = this.getGameModel();
		let Lang = this.getLangModel();
		let gamePlayers = Game.getPlayersModel();
		let actionName = Lang.getLine(
			firstOrSecond === 'first'
				? Role.getFirstActionName()
				: Role.getSecondActionName()
		);
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
		let typeInput = null;
		let submitButton = new ABuilder(
			'button',
			{
				'class': 'btn btn-primary btn-block mt-2',
				'type': 'submit',
			},
			Lang.getLine('submit')
		);

		let doNothingButton = new ABuilder(
			'button',
			{
				'class': 'btn btn-warning btn-block mt-2',
				'type': 'submit',
			},
			Lang.getLine('do_nothing')
		);
		let actionTitle = null;


		if (actionTargetType === 'ajax') {
			let params = doppel ? [{name : 'doppel', value : '1'}] : [];
			Ajax.post('player/action/first', params, (response) => {

				let i = 0;
				for (let action of response.data) {

					let btnType = i === 0 ? 'primary' : 'secondary';
					let actionButton = new ABuilder(
						'button',
						{
							'class': 'btn btn-block mt-2 btn-' + btnType,
							'type': 'button',
							'data-name': action.name,
							'data-type': action.type,
							'data-nbtargets': action.nbTargets,
							'data-targettype': action.targetType
						},
						Lang.getLine(action.name)
					);

					actionButton.on('click', () => {
						Role.setSecondActionName(actionButton.data('name'));
						Role.setSecondActionNbTargets(actionButton.data('nbtargets'));
						Role.setSecondActionTargetType(actionButton.data('targettype'));
						Role.setType(actionButton.data('type'));
						
						this.setRole(Role.toJSON());

						this.displayAction('second', newRole)
					});

					actionChoiceButtons.push(
						new ABuilder(
							'div',
							{'class' : 'col-sm-6'},
							actionButton
						)
					);
					i++;
				}
				
				let actionChoiceContainer = new ABuilder(
					'div',
					{'class' : 'row'},
					actionChoiceButtons
				);

				let actionForm = new ABuilder(
					'form',
					{
						'class': 'ajax-form play-and-vote-form',
						'data-target': 'player/action/' + firstOrSecond
					},
					[
						actionChoiceContainer,
					]
				);

				let doNothingForm = new ABuilder(
					'form',
					{
						'class': 'ajax-form do-nothing-form',
						'data-target': 'player/action/' + firstOrSecond
					},
					[
						new ABuilder(
							'input',
							{
								'type': 'hidden',
								'name': 'nothing',
								'value': '1'
							},
							''
						),
						doNothingButton
					]
				);


				$('.action-form-container').html('');
				$('.action-form-container')
					.append(actionTitle)
					.append(actionForm)
					.append(doNothingForm);

				let forms = new Forms();
				
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
				
				if(this.getRoleModel().getModel() === 'doppelganger' && player.getPlayerUid() === this.getPlayerUid()){
				
					continue;
					
				}
				
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
					'class': 'input-group mt-1'
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
									.clone()
									.attr('id', firstOrSecond + 'ActionPlayer' + j)
									.attr('name', 'player_' + j)
							)
							.find('label').html(Lang.getLine('player_' + j));

						break;
					case 'card':

						emptyInputGroup
							.append(
								cardsSelect
									.clone()
									.attr('id', firstOrSecond + 'ActionCard' + j)
									.attr('name', 'card_' + j)
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
			
			let doppelInput = doppel ? new ABuilder(
				'input',
				{
					'name': 'doppel',
					'value': 1,
					'type' : 'hidden'
				},
				''
			) : null;


			let actionForm = new ABuilder(
				'form',
				{
					'class': 'ajax-form play-and-vote-form',
					'data-target': 'player/action/' + firstOrSecond
				},
				[
					actionChoiceButtons,
					typeInput,
					doppelInput,
					targets
				]
			);

			let doNothingForm = new ABuilder(
				'form',
				{
					'class': 'ajax-form do-nothing-form',
					'data-target': 'player/action/' + firstOrSecond
				},
				[
					new ABuilder(
						'input',
						{
							'type': 'hidden',
							'name': 'nothing',
							'value': '1'
						},
						''
					),
					doNothingButton
				]
			);

			$('.action-form-container').html('');
			$('.action-form-container')
				.removeClass('d-none')
				.append(actionTitle)
				.append(actionForm)
				.append(firstOrSecond === 'first' ? doNothingForm : null);

			let forms = new Forms();

			if (actionIsPassive) {
				$('.action-form-container').addClass('d-none');
				actionForm.trigger('submit');
			}

		}

	}

	finishTurn() {

		let alert = new ABuilder('div', {
			'class': 'alert alert-light',
			'role': 'alert'
		}, this.getLangModel().getLine('finished'));

		$('.turn-finished').append(alert);

		$('.action-form-container').html('');

	}

	displayVote() {
		let Game = this.getGameModel();
		let Lang = this.getLangModel();
		let gamePlayers = Game.getPlayersModel();
		let submitButton = new ABuilder(
			'button',
			{
				'class': 'btn btn-primary btn-block mt-2',
				'type': 'submit',
			},
			Lang.getLine('vote')
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
			'name': 'playerUid',
			'class': 'custom-select',
		}, playersOptions);

		let voteForm = new ABuilder(
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

		$('.progress-role-name').html(this.getLangModel().getLine('now_vote'));
		$('.vote-message').html('');
		$('.action-form-container').html('');
		$('.action-form-container')
			.removeClass('d-none')
			.append(title)
			.append(voteForm);

		let forms = new Forms();

	}

}