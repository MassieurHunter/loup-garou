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


    if (!gamesPlayersForStarting.hasOwnProperty(roomUid)) {
      gamesPlayersForStarting[roomUid] = [];
    }

    gamesPlayersForStarting[roomUid].push(Player);

    if (gamesPlayersForStarting[roomUid].length === Game.getMaxPlayers()) {

      console.log('Starting game ' + Game.getCode());

      let randomPlayer = gamesPlayersForStarting[roomUid][Math.floor(Math.random() * gamesPlayersForStarting[roomUid].length)];
      let randomPlayerUid = randomPlayer.getPlayerUid();

      console.log(randomPlayer.getName() + ' has been chosen');

      gamesSockets[roomUid][randomPlayerUid].emit('message', {
        type: 'gameStart',
      });

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

      for (let Role of Game.getRolesModelForCasting()) {

        rolesforCasting.push(Role.getName());

      }

      for (let Role of Game.getRolesModelForRunning()) {

        rolesforRunning.push(Role.getName());

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

    sendNextRoleMessage(Game, gamesPlayersWithRoles[roomUid], Player.getRoleModel());

  });

  socket.on('disconnect', () => {

    console.log("user " + socket.id + " disconnected")

  });

});

function sendNextRoleMessage(Game, PlayersWithRole, lastRole = null) {

  let randomMicroTime = lastRole !== null ? (Math.floor(Math.random() * 15) + 15) * 1000 : 0;
  console.log('Entering send next role message');

  if(randomMicroTime){

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
        percent: Math.ceil(progress / total * 100),
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