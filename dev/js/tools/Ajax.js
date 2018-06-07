import ABuilder from './ABuilder';

class Ajax {

	get(aTarget, aData, aCallback) {

		let url = '/ajax';
		let data = aData || [];

		data.push({
			name: 'target',
			value: aTarget
		});

		$.get(url, data, (transport) => {
			// --
		}).always((aData) => {

			let data = {};

			if (aData.responseJSON) {
				data = aData.responseJSON;
			} else {
				data = aData;
			}

			let result = this.evaluate(data);

			if (aCallback) {
				aCallback(result);
			}

		});

	}

	post(aTarget, aData, aCallback) {

		let url = '/ajax';
		let data = aData || [];

		data.push({
			name: 'target',
			value: aTarget
		});

		$.post(url, data, (transport) => {
			// --
		}).always((aData) => {

			let data = {};

			if (aData.responseJSON) {
				data = aData.responseJSON;
			} else {
				data = aData;
			}

			let result = this.evaluate(data);

			if (aCallback) {
				aCallback(result);
			}

		});
	}

	evaluate(transport) {

		let responseData = transport;

		if (responseData.actions) {

			this.evaluateAction(responseData);

			delete(responseData.actions);
		}

		return responseData;
	}

	evaluateAction(pData) {

		let actions = pData.actions;
		let target = pData.formTarget;
		let form = $('FORM.ajax-form[data-target="' + target + '"]');

		$.each(actions, (index, action) => {

			switch (action.method) {

				case "error":

					if (form) {

						if (action.information) {

							let errorMessage = form.find('.form-message-error');

							if (errorMessage) {
								errorMessage.html(action.information);
								errorMessage.removeClass('d-none');
							}

						}

					}

					break;


				case "success":

					if (form) {

						if (action.information) {

							let errorMessage = form.find('.form-message-success');

							if (errorMessage) {
								errorMessage.html(action.information);
								errorMessage.removeClass('d-none');
							}

						}

					}

					break;

				case "load":
					setTimeout(() => location.href = action.location, action.timeout);
					break;
				case "postRedirect":
					this.postRedirect(action.location, action.params);
					break;
				case "reload":
					location.reload();
					break;
				case "insert":
					$(action.selector).html(action.content);
					break;
				case "append":
					$(action.selector).append(action.content);
					break;
				case "val":
					$(action.selector).val(action.value);
					break;
				case "alert":
					alert(action.information);
					break;
				case "delete":
					$(action.selector).remove();
					break;
				case "show":
					$(action.selector).show();
					break;
				case "hide":
					$(action.selector).hide();
					break;
				case "enableButton":
					$(action.selector).removeAttr('disabled');
					break;
				case "disableButton":
					$(action.selector).attr('disabled', 'disabled');
					break;
				case "attr":
					$(action.selector).attr(action.attr, action.value);
					break;
				case "removeAttr":
					$(action.selector).removeAttr(action.attr);
					break;
				case "trigger":
					$(action.selector).trigger(action.type);
					break;
				case "linkUpdateParams":
					this.linkUpdateParams(action.selector, action.params);
					break;
				case "socketMessage":
					let getUrl = window.location;
					let baseUrl = getUrl.protocol + "//" + getUrl.host;
					let socket = io.connect(baseUrl + ':3000');
					socket.emit(action.message, action.params);
					break;

				case "actionResult" :
					$('.action-results').append(
						new ABuilder('div', {
							'class': 'alert alert-primary',
							'role': 'alert'
						}, action.message)
					);
					break;
					
				case "vote" :
					$('.action-form-container').remove();
					$('.vote-message').append(
						new ABuilder('div', {
							'class': 'alert alert-primary',
							'role': 'alert'
						}, action.message)
					);
					break;
					
				case "gameResults" :
					$('.game-results').append(
						new ABuilder('div', {
							'class': 'alert alert-primary',
							'role': 'alert'
						}, action.message)
					);
					break;

				case "call":

					let callFunction = eval(action.name);

					if (callFunction !== "undefined") {

						let bindTarget = null;
						if (action.name.split('.').size() > 0) {
							let parts = action.name.split('.').slice(0, -1);
							bindTarget = eval(parts.join('.'));
						}

						if (action.parms) {
							callFunction.apply(bindTarget, action.parms);
						} else {
							callFunction();
						}

					} else {
						console.log('Function not found!');
					}
					break;
				case "class":

					let element = $(action.selector);

					if (element) {

						switch (action.type) {
							case "toggle":
								element.toggleClass(action.class);
								break;
							case "add":
								element.addClass(action.class);
								break;
							case "remove":
								element.removeClass(action.class);
								break;
						}

					}

					break;
			}

		});

	}

	postRedirect(aURL, aParams = []) {

		// build helper form
		let form = ABuilder('FORM', {'action': aURL, 'method': 'POST'});
		for (let key of Object.keys(aParams)) {
			let value = aParams[key];
			let input = ABuilder('INPUT', {'type': 'hidden', 'name': key, 'value': value});
			form.append(input);
		}

		// add form to DOM
		$('body').append(form);

		// submit form, aka redirecting ...
		form.submit();

	}

	/**
	 * Update a link url with the given parameters
	 * @param aSelector
	 * @param aParams
	 */
	linkUpdateParams(aSelector, aParams = []) {

		let element = $(aSelector);
		let href = element.attr('href');

		element.attr('href', href);

	}

}

export default new Ajax();