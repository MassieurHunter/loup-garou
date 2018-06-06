import PlayerModel from '../../dev/js/models/PlayerModel';
import RoleModel from '../../dev/js/models/RoleModel';
import GameModel from '../../dev/js/models/GameModel';
import http from 'http';

let server = http.createServer();
let io = require('socket.io').listen(server);
let gamesPlayersForStarting = {};
let gamesPlayersWithRoles = {};
let gamesSockets = {};

const {exec} = require('child_process');

io.sockets.on('connection', (socket) => {

	socket.emit('message', {
		type: 'connection',
		id: socket.id
	});

	socket.on('playerJoined', (data) => {

		let Player = new PlayerModel(data.player);
		let Game = new GameModel(data.game);
		let roomUid = 'game' + Game.getCode();

		console.log('player ' + Player.getName() + ' joined the game with code ' + Game.getCode());

		if (!gamesSockets.hasOwnProperty(roomUid)) {
			gamesSockets[roomUid] = [];
		}

		gamesSockets[roomUid][Player.getPlayerUid()] = socket;

		socket.join(roomUid);

		io.in(roomUid).emit('message', {
			type: 'playerJoined',
			player: Player.toJSON(),
			game: Game.toJSON()
		});

	});

	socket.on('gameStart', (data) => {

		let Player = new PlayerModel(data.player);
		let Game = new GameModel(data.game);
		let roomUid = 'game' + Game.getCode();

		console.log('game start message recieved');


		if (!gamesPlayersForStarting.hasOwnProperty(roomUid)) {
			gamesPlayersForStarting[roomUid] = [];
		}

		let playerPresent = false;

		for (let player of gamesPlayersForStarting[roomUid]) {

			if (player.getPlayerUid() === Player.getPlayerUid()) {

				playerPresent = true;

			}

		}

		if (!playerPresent) {

			gamesPlayersForStarting[roomUid].push(Player);

			if (gamesPlayersForStarting[roomUid].length === Game.getMaxPlayers()) {

				console.log('Starting game ' + Game.getCode());

				let randomPlayer = gamesPlayersForStarting[roomUid][Math.floor(Math.random() * gamesPlayersForStarting[roomUid].length)];
				let randomPlayerUid = randomPlayer.getPlayerUid();

				console.log(randomPlayer.getName() + ' has been chosen to start the game');

				gamesSockets[roomUid][randomPlayerUid].emit('message', {
					type: 'gameStart',
				});

			}

		}

	});

	socket.on('rolesInfos', (data) => {

		let Game = new GameModel(data.game);
		let roomUid = 'game' + Game.getCode();

		io.in(roomUid).emit('message', {
			type: 'rolesInfos',
			game: Game.toJSON()
		});

	});


	socket.on('roleTurn', (data) => {

		let Game = new GameModel(data.game);
		let Player = new PlayerModel(data.player);
		let roomUid = 'game' + Game.getCode();

		if (!gamesPlayersWithRoles.hasOwnProperty(roomUid)) {
			gamesPlayersWithRoles[roomUid] = [];
		}

		gamesPlayersWithRoles[roomUid].push(Player);

		if (gamesPlayersWithRoles[roomUid].length === Game.getNbPlayers()) {

			let rolesforCasting = [];
			let rolesforRunning = [];
			let playerRoles = [];

			for (let Role of Game.getRolesModelForCasting()) {

				rolesforCasting.push(Role.getName());

			}

			for (let Role of Game.getRolesModelForRunning()) {

				rolesforRunning.push(Role.getName());

			}

			for (let Player of gamesPlayersWithRoles[roomUid]) {

				playerRoles.push(Player.getName() + " ===> " + Player.getRoleModel().getName());

			}

			console.log('the game with code ' + Game.getCode() + ' has started');

			sendNextRoleMessage(Game, gamesPlayersWithRoles[roomUid]);

		}
	});

	socket.on('playerPlayedFirstAction', (data) => {
		let Player = new PlayerModel(data.player);
		let Game = new GameModel(data.game);
		let Role = new RoleModel(data.role);
		let Newrole = data.newRole ? new RoleModel(data.newRole) : new RoleModel();
		let doppel = data.doppel;
		let roomUid = 'game' + Game.getCode();

		console.log('player ' + Player.getName() + ' finished first role action (' + Role.getName() + ')');

		gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
			type: 'playerPlayedFirstAction',
			game: Game.toJSON(),
			role: Role.toJSON(),
			newRole: Newrole.toJSON(),
			doppel: doppel,
		});

	});

	socket.on('playerFinishedTurn', (data) => {

		let Player = new PlayerModel(data.player);
		let Game = new GameModel(data.game);
		let Role = new RoleModel(data.role);
		let roomUid = 'game' + Game.getCode();

		console.log('player ' + Player.getName() + ' finished his turn (' + Role.getName() + ')');

		gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
			type: 'playerFinishedTurn',
		});

		let sendMessage = true;
		let nbLoups = 0;
		let nbFrancMacs = 0;
		let firstLoupUid = 0;
		let firstFrancMacUid = 0;

		if (Role.getModel() === 'loup') {

			for (let player of gamesPlayersWithRoles[roomUid]) {

				if (player.getRoleModel().getModel() === 'loup') {
					
					if (nbLoups === 0) {
						
						firstLoupUid = player.getPlayerUid();
						
					}
					
					nbLoups++;
				}

			}

			if (nbLoups === 2) {
				
				if (Player.getPlayerUid() !== firstLoupUid) {
					
					sendMessage = false;
					
				}
				
			}

		}

		if (Role.getModel() === 'francmac') {

			for (let player of gamesPlayersWithRoles[roomUid]) {

				if (player.getRoleModel().getModel() === 'francmac') {

					if (nbFrancMacs === 0) {

						firstFrancMacUid = player.getPlayerUid();

					}
					
					nbFrancMacs++;
				}

			}

			if (nbFrancMacs === 2) {

				if (Player.getPlayerUid() !== firstFrancMacUid) {

					sendMessage = false;

				}

			}

		}

		if (sendMessage) {

			sendNextRoleMessage(Game, gamesPlayersWithRoles[roomUid], Role);

		}

	});

	socket.on('disconnect', () => {

		console.log("user " + socket.id + " disconnected")

	});

});

function sendNextRoleMessage(Game, PlayersWithRole, lastRole = null) {

	let randomMicroTime = lastRole !== null ? (Math.floor(Math.random() * 10) + 10) * 1000 : 0;
	console.log('Entering send next role message');

	if (randomMicroTime) {

		console.log("Waiting " + (randomMicroTime / 1000) + 's')

	}

	setTimeout(() => {

		let roomUid = 'game' + Game.getCode();
		let NextRole = new RoleModel();
		let nextRoleFound = false;
		let total = Game.getRolesModelForRunning().length;
		let progress = 0;

		for (let Role of Game.getRolesModelForRunning()) {
			progress++;

			if (lastRole !== null) {

				if (nextRoleFound) {

					NextRole = Role;
					break;

				} else if (lastRole.getModel() === Role.getModel()) {

					nextRoleFound = true;

				}

			} else {

				NextRole = Role;
				break;

			}

		}

		console.log('Next Role', NextRole.getModel());

		if (NextRole.getModel() !== null) {

			let roleHasPlayer = false;

			for (let Player of PlayersWithRole) {

				if (Player.getRoleModel().getModel() === NextRole.getModel()) {

					roleHasPlayer = true;
					break;

				}

			}

			io.in(roomUid).emit('message', {
				type: 'roleTurn',
				role: NextRole.toJSON(),
				progress: Math.ceil((progress  - 1) / total * 100),
			});

			console.log('message sent');

			if (!roleHasPlayer) {

				console.log(NextRole.getName() + " is in the middle");

				sendNextRoleMessage(Game, PlayersWithRole, NextRole)

			}

		} else {

			console.log("no more roles, let's finish the game");

			io.in(roomUid).emit('message', {
				type: 'actionsFinished'
			});

		}

	}, randomMicroTime);

}

server.listen(3000, () => {
	console.log('listening on *:3000');
});