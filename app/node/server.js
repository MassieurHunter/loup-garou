import PlayerModel from '../../dev/js/models/PlayerModel';
import RoleModel from '../../dev/js/models/RoleModel';
import GameModel from '../../dev/js/models/GameModel';
import LangModel from '../../dev/js/models/LangModel';
import http from 'http';

let server = http.createServer();
let io = require('socket.io').listen(server);
let clients = {};
let games = {};
let Lang = {};

const {exec} = require('child_process');

io.sockets.on('connection', (socket) => {
    console.log("user " + socket.id + " connected");

    socket.emit('message', {
        type: 'connection',
        id: socket.id
    });

    socket.on('playerJoined', (data) => {

        let Player = new PlayerModel(data.player);
        let Game = new GameModel(data.game);
        let roomUid = 'game' + Game.getGameUid();

        console.log('player ' + Player.getName() + ' joined the game with code ' + Game.getCode());

        socket.join(roomUid);

        socket.broadcast.to(roomUid).emit('message', {
            type: 'playerJoined',
            player: Player.toJSON(),
            game: Game.toJSON()
        });

    });

    socket.on('gameStart', (data) => {
        let Game = new GameModel(data.game);
        let FirstRole = new RoleModel(data.firstRole);
        let roomUid = 'game' + Game.getGameUid();

        console.log(' the game with code ' + Game.getCode() + ' has started');

        io.in(roomUid).emit('message', {
            type: 'gameStart',
            game: Game.toJSON(),
            firstRole: FirstRole.toJSON()
        });

    });

    socket.on('playerPlayedFirstAction', (data) => {
        let Player = new PlayerModel(data.player);
        let Game = new GameModel(data.game);
        let Role = new RoleModel(data.role);
        let roomUid = 'game' + Game.getGameUid();

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
        let Role = new RoleModel(data.role);
        let NextRole = new RoleModel(data.nextRole);
        let roomUid = 'game' + Game.getGameUid();

        console.log('player ' + Player.getName() + ' finished his turn (' + Role.getName() + ')');

        if (NextRole.getName()) {

            io.in(roomUid).emit('message', {
                type: 'playerPlayedFirstAction',
                game: Game.toJSON(),
                nextRole: NextRole.toJSON()
            });

        } else {

            io.in(roomUid).emit('message', {
                type: 'actionsFinished'
            });

        }
    });

    socket.on('disconnect', () => {

        console.log("user " + socket.id + " disconnected")

    });

});

server.listen(3000, () => {
    console.log('listening on *:3000');
});