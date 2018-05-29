import PlayerModel from '../../dev/js/models/PlayerModel';
import RoleModel from '../../dev/js/models/RoleModel';
import GameModel from '../../dev/js/models/GameModel';
import http from 'http';

let server = http.createServer();
let io = require('socket.io').listen(server);
let gamesPlayers = {};

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

    socket.join(roomUid);

    io.in(roomUid).emit('message', {
      type: 'playerJoined',
      player: Player.toJSON(),
      game: Game.toJSON()
    });

  });

  socket.on('gameStart', (data) => {

    let Game = new GameModel(data.game);
    let Player = new PlayerModel(data.player);
    let roomUid = 'game' + Game.getCode();

    if (!gamesPlayers.hasOwnProperty(roomUid)) {
      gamesPlayers[roomUid] = [];
    }

    gamesPlayers[roomUid].push(Player);

    if (gamesPlayers[roomUid].length === Game.getNbPlayers()) {

      let rolesforCasting = [];
      let rolesforRunning = [];

      for (let Role of Game.getRolesModelForCasting()) {

        rolesforCasting.push(Role.getName());

      }

      for (let Role of Game.getRolesModelForRunning()) {

        rolesforRunning.push(Role.getName());

      }

      console.log('the game with code ' + Game.getCode() + ' has started');

      io.in(roomUid).emit('message', {
        type: 'gameStart',
        game: Game.toJSON(),
        rolesforCasting: rolesforCasting,
        rolesForRunning: rolesforRunning,
      });


      sendNextRoleMessage(Game, gamesPlayers[roomUid]);

    }

  });

  socket.on('playerPlayedFirstAction', (data) => {
    let Player = new PlayerModel(data.player);
    let Game = new GameModel(data.game);
    let Role = new RoleModel(data.role);
    let roomUid = 'game' + Game.getCode();

    console.log('player ' + Player.getName() + ' finished first role action (' + Role.getName() + ')');

    io.in(roomUid).emit('message', {
      type: 'playerPlayedFirstAction',
      game: Game.toJSON(),
      role: Role.toJSON()
    });

  });

  socket.on('playerFinishedTurn', (data) => {
    let Player = new PlayerModel(data.player);
    let Game = new GameModel(data.game);
    let roomUid = 'game' + Game.getCode();

    console.log('player ' + Player.getName() + ' finished his turn (' + Player.getRoleModel().getName() + ')');

    sendNextRoleMessage(Game, gamesPlayers[roomUid], Player.getRoleModel());

  });

  socket.on('disconnect', () => {

    console.log("user " + socket.id + " disconnected")

  });

});

function sendNextRoleMessage(Game, PlayersWithRole, lastRole = null) {

  console.log('entering send next role message');

  let roomUid = 'game' + Game.getCode();
  let NextRole = new RoleModel();
  let nextRoleFound = false;

  for (let Role of Game.getRolesModelForRunning()) {

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
    });

    console.log('message sent');

    if (!roleHasPlayer) {

      let randomMicroTime = (Math.floor(Math.random() * 30) + 30) * 1000;

      console.log(NextRole.getName() + " is in the middle, we'll wait " + (randomMicroTime/1000) + 's');


      setTimeout(() => {

        sendNextRoleMessage(Game, PlayersWithRole, NextRole)

      }, randomMicroTime);

    }

  } else {

    console.log("no more roles, let's finish the game");

    io.in(roomUid).emit('message', {
      type: 'actionsFinished'
    });

  }

}

server.listen(3000, () => {
  console.log('listening on *:3000');
});