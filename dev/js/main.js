import Forms from './components/Forms';

let loupGraou = {

  init() {
    this.bootstrap = require('bootstrap');
    this.jquery = require('jquery');
    this.forms = new Forms();

    this.listenCreateGame();

  },

  listenCreateGame(){
    $('.max-players-range').on('input', (event) => {
      let range = $(event.target)
      $('.nb-max-players').html(range.val());
    }).trigger('input')
  },


}

loupGraou.init();