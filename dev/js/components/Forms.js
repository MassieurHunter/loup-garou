//
// Tools

import Ajax from '../tools/Ajax';
import Constants from '../tools/Constants';

class Forms {

  constructor() {

    this.forms = $('FORM');

    this.forms.each((index, element) => {

      let form = $(element);

      this.onSubmit(form);

    })

  }

  onSubmit(form) {

    let target = form.data('target');

    form.on('submit', (event) => {

      // prevent real submit
      event.stopPropagation();
      event.preventDefault();

      this.clearMessages(form);

      //fields
      let fields = form.serializeArray();

      Ajax.post(target, fields, (aResponse) => {



      });



    })

  }

  clearMessages(form) {
    form.find('.form-message').html('').addClass('d-none')
  }

}

export default Forms;