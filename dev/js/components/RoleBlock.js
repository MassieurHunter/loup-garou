import AData from "../tools/AData";
import ABuilder from "../tools/ABuilder";

export default class RoleBlock {

	constructor(aData) {
		this._data = new AData(aData);
	}

	render() {
		let data = this._data;


		if (data.get('uid')) {
			let services = data.getObject('services');
			/*
			 * Image
			 */
			let image = ABuilder('div', {'class': 'service-point-image-container'}, [
				ABuilder('img', {'src': data.get('image') ? data.get('image') : (services.has(1) ? '/typo3conf/ext/apartbcee/Resources/Public/Icons/nopic/nopic-agence.svg' : '/typo3conf/ext/apartbcee/Resources/Public/Icons/nopic/nopic-S-bank.svg')})
			]);

			/*
			 * Close button
			 */
			let closeButton = ABuilder('div', {'class': 'bcee-button-action bcee-icon bcee-ab-close active service-point-close'}, []);

			/*
			 * Type List
			 */
			let type = [];

			/*
			 * VCard
			 */
			let showVcard = true;

			if (services.has(10)) {
				type.push(
					ABuilder('span', {}, [
						services.get(10)
					])
				);
			} else if (services.has(1)) {
				type.push(
					ABuilder('span', {}, [
						services.get(1)
					])
				);
			} else {
				showVcard = false;
			}

			if (services.has(2)) {
				type.push(
					ABuilder('span', {}, [
						services.get(2)
					])
				);
			}

			/*
			 * Name and Type
			 */
			let distance = parseFloat(data.get('distance'));
			let mainInfos = ABuilder('div', {'class': 'service-point-infos service-point-main-infos'}, [
				ABuilder('h4', {'class': 'service-point-name'}, [
					data.get('name')
				]),
				ABuilder('div', {'class': 'service-point-type-and-distance'}, [
					ABuilder('div', {'class': 'service-point-distance'}, [
						(distance < 1 ? ( Math.round(distance * 1000)) + 'm' : (Math.round(distance * 10) / 10) + 'km') + ' - '
					]),
					ABuilder('div', {'class': 'service-point-type'}, type)
				])
			]);

			/*
			 * Opening Hours List with Periods
			 */
			let openingHoursList = [];
			let hasPeriods = false;

			$.each(data.getObject('openinghours').getData(), (index, openingHour) => {
				/*
				 * Periods list for specific opening hours
				 */
				let periodsList = [];

				let aDataOpeningHour = new AData(openingHour);

				if (aDataOpeningHour.getObject('period').getData().length == 0) {
					periodsList.push(
						$('#trad-closed').html()
					);
				} else {
					$.each(aDataOpeningHour.getObject('period').getData(), (index2, period) => {
						hasPeriods = true;
						let aDataPeriod = new AData(period);
						periodsList.push(
							ABuilder('li', {'class': 'service-point-opening-hour-period'}, [
								aDataPeriod.get('start') + ' - ' + aDataPeriod.get('end')
							]),
						);
					});
				}


				openingHoursList.push(
					ABuilder('li', {'class': 'service-point-opening-day'}, [
						ABuilder('span', {'class': 'service-point-day-name'}, [
							aDataOpeningHour.get('day'),
						]),
						ABuilder('ul', {'class': 'service-point-opening-hour-periods-list'}, periodsList)
					]),
				);


			});

			/*
			 * Status + Opening Hours
			 */
			let hoursWrapper = '';

			if (hasPeriods) {
				hoursWrapper = ABuilder('span', {'class': 'service-point-opening-hours-wrapper'}, [
					ABuilder('ul', {'class': 'service-point-opening-hours'}, [
						openingHoursList
					]),
					ABuilder('span', {'class': 'tick'}, []),
				])
			}

			let status = ABuilder('div', {'class': 'service-point-infos service-point-status-wrapper border-bottom'}, [
				ABuilder('span', {'class': 'service-point-status-text ' + (hasPeriods ? 'with-hours' : '')}, [
					data.get('statusTranslated'),
				]),
				ABuilder('span', {'class': 'service-point-status-icon status-' + data.get('status')}),
				hoursWrapper
			]);

			/*
			 * Full Address
			 */
			let address = '';
			if (data.get('adresse')) {
				address = ABuilder('div', {'class': 'service-point-infos service-point-address-wrapper'}, [
					ABuilder('span', {'class': 'left-icon bcee-icon bcee-button-action bcee-ab-directions'}, []),
					data.get('adresse'),
					'<br/>',
					data.get('zipcode'),
					(data.get('zipcode') && data.get('city') ? ', ' : ''),
					data.get('city'),
					ABuilder('div', {'class': 'service-point-view-on-map visible-xs-block visible-sm-block'}, [
						$('#trad-show-map').html()
					])
				]);
			}

			/*
			 * Telephone and Fax
			 */
			let phone = '';
			let phoneFax = '';
			if (data.get('phone')) {
				phone = ABuilder('div', {'class': 'service-point-phone'}, [
					$('#trad-tel').html() + ': ',
					ABuilder('a', {'href': 'tel://' + data.get('phone').replace(/ /gi, '-')}, [
						data.get('phone')
					])
				]);
			}
			if (phone) {
				phoneFax = ABuilder('div', {'class': 'service-point-infos service-point-phone-fax-wrapper'}, [
					ABuilder('a', {
						'class': 'left-icon bcee-icon bcee-button-action bcee-ab-call',
						'href': 'tel://' + data.get('phone').replace(/ /gi, '-')
					}, []),
					phone
				]);
			}

			/*
			 * Vcard
			 */
			let vcard = '';
			if (showVcard) {
				vcard = ABuilder('div', {'class': 'service-point-infos service-point-vcard-wrapper'}, [
					ABuilder('a', {
						'class': 'left-icon bcee-icon bcee-button-action bcee-ab-download',
						'href': URL.getUrlForAPI() + '?action=vcard&servicePointUid=' + data.get('uid')
					}, []),
					ABuilder('div', {'class': 'service-point-vcard'}, [
						ABuilder('a', {'href': URL.getUrlForAPI() + '?action=vcard&servicePointUid=' + data.get('uid')}, [
							$('#trad-vcard').html()
						])
					]),
				]);
			}

			/*
			 * Contact
			 */
			let contact = '';

			if (services.has(1) && data.get('contact')) {
				contact = ABuilder('div', {'class': 'service-point-infos service-point-contact border-bottom'}, [
					ABuilder('a', {
						'class': 'left-icon bcee-icon bcee-button-action bcee-ab-mark-read',
						'href': 'mailto:' + data.get('contact')
					}, []),
					ABuilder('a', {'href': 'mailto:' + data.get('contact')}, [
						$('#trad-contact').html()
					])
				]);
			}

			/*
			 * Service List
			 */
			let servicesList = [];
			let servicesSBankList = [];

			$.each(services.getData(), (index, service) => {
				if (!ignoredServicesID.hasOwnProperty(index)) {

					if (!hiddenServicesIDList.hasOwnProperty(index) && !sBankServicesIDList.hasOwnProperty(index) && service != null) {
						servicesList.push(
							ABuilder('li', {'class': 'service-point-service'}, [
								ABuilder('span', {'class': 'bcee-icon bcee-icon-check'}, []),
								ABuilder('span', {'class': 'bcee-servicepoint-service-text'}, [service]),
							])
						);
					}

					if (sBankServicesIDList.hasOwnProperty(index)) {
						servicesSBankList.push(
							ABuilder('li', {'class': 'service-point-service'}, [
								ABuilder('span', {'class': 'bcee-icon bcee-icon-check'}, []),
								ABuilder('span', {'class': 'bcee-servicepoint-service-text'}, [service]),
							])
						);
					}
					
				}
			});

			let servicesBlock = '';
			let servicesBlockTitle = '';
			let servicesSBankBlock = '';
			let servicesSBankBlockTitle = '';
			if (servicesList.length > 0) {
				servicesBlockTitle = ABuilder('div', {'class': 'bcee-form-label space-bottom'}, [
					$('#trad-service-section').html()
				]);
				servicesBlock = ABuilder('ul', {'class': 'service-point-infos service-point-services-list ' + (servicesSBankList.length == 0 ? 'border-bottom' : '')}, [
					servicesList
				]);
			}
			if (servicesSBankList.length > 0) {
				servicesSBankBlockTitle = ABuilder('div', {'class': 'bcee-form-label space-bottom'}, [
					$('#trad-service-sbank-section').html()
				]);
				servicesSBankBlock = ABuilder('ul', {'class': 'service-point-infos service-point-services-list border-bottom'}, [
					servicesSBankList
				]);
			}

			let additionalInformations = '';

			if (data.get('information')) {
				additionalInformations = ABuilder('ul', {'class': 'service-point-infos service-point-additional-informations border-bottom'}, [
					data.get('information')
				]);
			}

			let langList = [];

			$.each(data.getObject('langs').getData(), (index, lang) => {
				let aDataLang = new AData(lang);
				let flagClone = $('.round-flag.flag-' + aDataLang.get('iso') + ':not(.clone)').clone().addClass('clone');
				langList.push(flagClone);
			});

			let langBlock = ABuilder('div', {'class': 'service-point-infos service-point-langs'}, [
				ABuilder('div', {'class': 'service-point-lang-title bcee-form-label space-bottom'}, [
					$('#trad-languages').html(),
				]),
				ABuilder('div', {'class': 'service-point-langs-list'}, [
					langList
				])

			]);

			$('.service-point-detail').html(
				ABuilder('div', {'class': 'bcee-service-point service-point-search-detail'}, [
					image,
					closeButton,
					ABuilder('div', {'class': 'service-point-infos-wrapper'}, [
						mainInfos,
						status,
						address,
						phoneFax,
						vcard,
						contact,
						servicesBlockTitle,
						servicesBlock,
						servicesSBankBlockTitle,
						servicesSBankBlock,
						additionalInformations,
						langBlock
					])
				])
			).attr('data-uid', data.get('uid'));

			this.attachEvents();
		} else {
			let servicePointList = $('.service-point-list-wrapper');
			let servicePointDetails = $('.service-point-detail-wrapper');
			let loader = servicePointDetails.find('.bcee-loader-panel');
			let details = servicePointDetails.find('.service-point-detail');

			servicePointList.removeClass('rolled-in');

			loader.css({display: 'none'});
			details.css({display: 'none'});
		}
	}

	attachEvents() {
		$('.service-point-detail').find('[data-toggle="tooltip"]').tooltip();

		let hoursWrapper = $('.service-point-opening-hours-wrapper');

		hoursWrapper.on('click', () => {
			$('.service-point-status-wrapper').toggleClass('expanded');
		});

		$('.service-point-view-on-map').on('click', () => {
			$('html, body').animate(
				{scrollTop: $('.map-search-wrapper:first').offset().top}, 500
			);
		});

		$('.service-point-address-wrapper .bcee-button-action').on('click', () => {
			let lat = this._data.get('latitude');
			let lng = this._data.get('longitude');
			let latlng = lat + ',' + lng;

			if ((navigator.platform.indexOf("iPhone") != -1)
				|| (navigator.platform.indexOf("iPod") != -1)
				|| (navigator.platform.indexOf("iPad") != -1)) {
				window.open("http://maps.apple.com/?q=" + latlng);
			}
			else {
				window.open("http://maps.google.com/maps?q=" + latlng);
			}
		});

	}

	close() {
		let servicePointListWrapper = $('.service-point-list-wrapper');
		servicePointListWrapper.removeClass('rolled-in');
	}

}