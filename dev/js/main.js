import Ajax from './tools/Ajax';
import Forms from './components/Forms';
import Noty from 'noty';
import PlayerModel from './models/PlayerModel';
import $ from 'jquery';

let loupGarou = {

    init() {
        this.bootstrap = require('bootstrap');
        this.forms = new Forms();
        this.player = new PlayerModel();

        Noty.overrideDefaults({
            theme: 'bootstrap-v4',
            layout: 'bottom'
        });

        this.listenCreateGame();
        this.listenThemeChange();
        this.play();

    },

    listenCreateGame() {
        let range = $('.max-players-range');

        range.on('input', (event) => {
            $('.nb-max-players').html(range.val());
        }).trigger('input')
    },

    listenThemeChange() {

        let themeSelector = $('.themes-selector');

        themeSelector.on('change', (event) => {

            let themeStyle = $('link[data-type="theme"]');
            themeStyle.attr('href', '');

            if (themeSelector.val() !== '') {
                themeStyle.attr('href', 'css/' + themeSelector.val() + '/bootstrap.min.css');
            }

        });

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
                            this.player = new PlayerModel(response.player);
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
                                this.player.setGame(response.game);
                                this.player.setRole(response.role);
                            });
                        }

                        break;
                }
            })
        }
    }


}

loupGarou.init();