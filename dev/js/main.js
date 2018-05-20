import Ajax from './tools/Ajax';
import Forms from './components/Forms';

let loupGraou = {

    init() {
        this.bootstrap = require('bootstrap');
        this.jquery = require('jquery');
        this.forms = new Forms();

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
            let socket = io.connect( baseUrl + ':3000');
            socket.on('message', (message) => {
                switch (message.type){
                    case 'connection' :
                        Ajax.post('socket/connection', {'socketUid': message.id});
                        break;
                }
            })
        }
    }


}

loupGraou.init();