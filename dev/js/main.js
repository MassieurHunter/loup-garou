import Forms from './components/Forms';

let loupGraou = {

  init() {
    this.bootstrap = require('bootstrap');
    this.jquery = require('jquery');
    this.forms = new Forms();

    this.listenCreateGame();
      this.play();

  },

  listenCreateGame(){
    $('.max-players-range').on('input', (event) => {
      let range = $(event.target)
      $('.nb-max-players').html(range.val());
    }).trigger('input')
  },

  play() {
      if($('#play-socket').val() === '1'){
        let socket = io.connect('http://192.168.10.10:3000');
      }
  }


}

loupGraou.init();