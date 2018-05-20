let http = require('http');
let server = http.createServer();
let io = require('socket.io').listen(server);

let clients = {};

io.sockets.on('connection', (socket) => {
    clients[socket.id] = true;
    console.log("user " + socket.id + " connected");

    socket.emit('message', {
        type: 'connection',
        id: socket.id
    });

    socket.on('playerConnected', () => {

        delete clients[socket.id];
        console.log("user " + socket.id + " disconnected")

    });

    socket.on('disconnect', () => {

        delete clients[socket.id];
        console.log("user " + socket.id + " disconnected")

    });

});

server.listen(3000, () => {
    console.log('listening on *:3000');
});