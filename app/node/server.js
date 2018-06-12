import PlayerModel from '../../dev/js/models/PlayerModel';
import RoleModel from '../../dev/js/models/RoleModel';
import GameModel from '../../dev/js/models/GameModel';
import https from 'https';
import fs from 'fs';

let ssl_options = {
	key: fs.readFileSync('/etc/nginx/ssl/loup-garou.local.key'),
	cert: fs.readFileSync('/etc/nginx/ssl/loup-garou.local.crt')
};

let server = https.createServer(ssl_options);
let io = require('socket.io').listen(server);
let gamesPlayersForStarting = {};
let gamesPlayersVoted = {};
let gamesPlayersWithRoles = {};
let gamesSockets = {};
let gamesProgress = {};

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

		socket.join(roomUid);

		if (gamesSockets[roomUid].hasOwnProperty(Player.getPlayerUid())) {

			gamesSockets[roomUid][Player.getPlayerUid()] = socket;

			io.in(roomUid).emit('message', {
				type: 'playerRejoined',
				player: Player.toJSON(),
				game: Game.toJSON()
			});

		} else {

			gamesSockets[roomUid][Player.getPlayerUid()] = socket;

			io.in(roomUid).emit('message', {
				type: 'playerJoined',
				player: Player.toJSON(),
				game: Game.toJSON()
			});

		}

	});

	socket.on('playerRejoined', (data) => {

		let Game = new GameModel(data.game);
		let roomUid = 'game' + Game.getCode();
		let Player = new PlayerModel(data.player);

		gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
			type: 'rolesInfos',
			game: Game.toJSON()
		});
		
		setTimeout(() => {

			if (gamesProgress.hasOwnProperty(roomUid)) {
				
				console.log(gamesProgress[roomUid].currentRole.getName());

				if (gamesProgress[roomUid].currentRole.getName() !== null) {

					gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
						type: 'roleTurn',
						role: gamesProgress[roomUid].currentRole.toJSON(),
						progress: gamesProgress[roomUid].progress,
					});

					gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
						type: 'rebuildActions'
					});

				} else {

					gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
						type: 'rebuildActions'
					});

					gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
						type: 'actionsFinished'
					});

					gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
						type: 'rebuildVote'
					});

				}

			}

		}, 1000);

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

				if (!gamesProgress.hasOwnProperty(roomUid)) {

					gamesProgress[roomUid] = {
						progress: 0,
						currentRole: new RoleModel(),
					};

				}

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
		let refresh = data.refresh;
		
		if(refresh){
			
			gamesSockets[roomUid][Player.getPlayerUid()].emit('message', {
				type: 'playerFinishedTurn',
			});
			
			return;
		}

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

	socket.on('playerVoted', (data) => {

		let Player = new PlayerModel(data.player);
		let Game = new GameModel(data.game);
		let roomUid = 'game' + Game.getCode();

		console.log('player ' + Player.getName() + ' has voted');

		if (!gamesPlayersVoted.hasOwnProperty(roomUid)) {
			gamesPlayersVoted[roomUid] = [];
		}

		gamesPlayersVoted[roomUid].push(Player);

		io.in(roomUid).emit('message', {
			type: 'playerVoted',
			nbVotes: gamesPlayersVoted[roomUid].length
		});

		if (gamesPlayersVoted[roomUid].length === gamesPlayersWithRoles[roomUid].length) {

			console.log('game ' + Game.getCode() + ' is finished');
			delete gamesPlayersVoted[roomUid];
			delete gamesPlayersWithRoles[roomUid];
			delete gamesPlayersForStarting[roomUid];
			delete gamesSockets[roomUid];

		}


	});
	
	socket.on('playerCanceledVote', (data) => {

		let Player = new PlayerModel(data.player);
		let Game = new GameModel(data.game);
		let roomUid = 'game' + Game.getCode();

		console.log('player ' + Player.getName() + ' has canceled his vote');

		if (!gamesPlayersVoted.hasOwnProperty(roomUid)) {
			gamesPlayersVoted[roomUid] = [];
		}

		let oldGamesPlayersVoted = gamesPlayersVoted[roomUid];
		gamesPlayersVoted[roomUid] = [];

		for (let loopPlayer of oldGamesPlayersVoted) {

			if (loopPlayer.getPlayerUid() !== Player.getPlayerUid()) {

				gamesPlayersVoted[roomUid].push(loopPlayer);

			}

		}

		io.in(roomUid).emit('message', {
			type: 'playerCanceledVote',
			player: Player.toJSON(),
			nbVotes: gamesPlayersVoted[roomUid].length
		});

	});

	socket.on('disconnect', () => {

		console.log("user " + socket.id + " disconnected")

	});

});

function sendNextRoleMessage(Game, PlayersWithRole, lastRole = null) {

	let randomMicroTime = lastRole !== null ? (Math.floor(Math.random() * 5) + 5) * 1000 : 0;
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

			let percent = Math.ceil(progress / total * 100);

			gamesProgress[roomUid] = {
				progress: percent,
				currentRole: NextRole,
			};

			io.in(roomUid).emit('message', {
				type: 'roleTurn',
				role: NextRole,
				progress: percent,
			});

			console.log('message sent');

			if (!roleHasPlayer) {

				console.log(NextRole.getName() + " is in the middle");

				sendNextRoleMessage(Game, PlayersWithRole, NextRole)

			}

		} else {

			gamesProgress[roomUid] = {
				progress: 100,
				currentRole: new RoleModel,
			};

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