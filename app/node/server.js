let http = require('http');
let server = http.createServer();
let io = require('socket.io').listen(server);
let clients = {};
let games = {};
let lang = {};

const { exec } = require('child_process');

exec('php index.php lang', (err, stdout, stderr) => {
    if (err) {
        // node couldn't execute the command
        return;
    }

    lang = JSON.parse(stdout);
});


io.sockets.on('connection', (socket) => {
    console.log("user " + socket.id + " connected");

    socket.emit('message', {
        type: 'connection',
        id: socket.id
    });

    socket.on('playerJoined', (data) => {

        let Player = data.player;
        let Game = data.game;
        let roomUid = 'game' + Game.gameUid;

        console.log('player ' + Player.name + ' joined the game with code ' + Game.code);

        socket.join(roomUid);

        socket.broadcast.to(roomUid).emit('message', {
            type: 'playerJoined',
            text: lang.player_joined_game.replace('*playername*', Player.name),
            nbPlayers: Game.nbPlayers,
            gameReady: Game.nbPlayers === Game.maxPlayers,
        });

    });

    socket.on('disconnect', () => {

        console.log("user " + socket.id + " disconnected")

    });

});

server.listen(3000, () => {
    console.log('listening on *:3000');
});