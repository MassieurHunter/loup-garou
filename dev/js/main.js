import Ajax from './tools/Ajax';
import Forms from './components/Forms';
import Noty from 'noty';
import GameModel from './models/GameModel';
import PlayerModel from './models/PlayerModel';
import $ from 'jquery';

let loupGraou = {

    init() {
        this.bootstrap = require('bootstrap');
        this.forms = new Forms();

        Noty.overrideDefaults({
            theme: 'bootstrap-v4',
            layout: 'bottom'
        });

        this.listenCreateGame();
        this.play();

    },

    listenCreateGame() {
        $('.max-players-range').on('input', (event) => {
            let range = $(event.target);
            $('.nb-max-players').html(range.val());
        }).trigger('input')
    },

    play() {
        if ($('#play-socket').val() === '1') {
            let getUrl = window.location;
            let baseUrl = getUrl.protocol + "//" + getUrl.host;
            let socket = io.connect(baseUrl + ':3000');
            socket.on('message', (message) => {
                switch (message.type) {
                    case 'connection' :
                        Ajax.post('socket/connection', [], (response) => {
                            socket.emit('playerJoined', response.data);
                        });
                        break;
                    case 'playerJoined' :
                        new Noty({
                            type: 'info',
                            text: message.text
                        }).show();

                        $('.nb-players').html(message.nbPlayers);

                        if (message.gameReady) {
                            Ajax.post('game/start', [], (response) => {

                            });
                        }

                        break;
                }
            })
        }
    }


}

loupGraou.init();