$(function () {

	// Get record button MDL name from HTML
	var recIconName = $('.record-button').eq(0).next('i').text();

	// Get current recording status and set button state
	var pauseHeartbeat = false;
	var heartbeatXHR;
	setInterval(function () {
		if (!pauseHeartbeat) {
			heartbeatXHR = $.getJSON('/api/' + api_key + '/stream/ping', function (info) {
				$('.record-button').each(function () {
					var channel = $(this).parent('label').parent('td').parent('tr').attr('channel');
					if (typeof info[channel] !== 'undefined') {
						if (info[channel].recording === true && $(this).not(':checked')) {
							$(this).prop('checked', true);
							$(this).parent('label').addClass('is-checked');
							$(this).next('i').text('stop');
						} else if (info[channel].recording === false && $(this).is(':checked')) {
							$(this).prop('checked', false);
							$(this).parent('label').removeClass('is-checked');
							$(this).next('i').text(recIconName);
						}
					}
				});
			});
		}
	}, 500);

	// Record/Stop Recording on button click
	$('.record-button').click(function () {
		pauseHeartbeat = true;
		heartbeatXHR.abort();
		var channel = $(this).parent('label').parent('td').parent('tr').attr('channel');
		var icon = $(this).next('i');
		if ($(this).is(':checked')) {
			icon.text('stop');
			$.getJSON('/api/' + api_key + '/stream/record-start/' + channel, function (recordingPath) {
				if (recordingPath === "") {
					console.log('Error starting recording');
				} else {
					console.log(recordingPath);
				}
				pauseHeartbeat = false;
			});
		} else {
			icon.text(recIconName);
			$.getJSON('/api/' + api_key + '/stream/record-stop/' + channel, function (recordingPath) {
				if (recordingPath === "") {
					console.log('Error stopping recording');
				} else {
					console.log(recordingPath);
				}
				pauseHeartbeat = false;
			});
		}
	});

	$('.sub-button').click(function () {
		var channel = $(this).attr('channel');
		if ($(this).is(':checked')) {
			$.getJSON('/api/' + api_key + '/subscription/remove/' + channel, function (result) {
				if (result === false) {
					console.log('Error unsubscribing');
				} else {
					console.log(result);
				}
			});
		} else {
			$.getJSON('/api/' + api_key + '/subscription/add/' + channel, function (result) {
				if (result === false) {
					console.log('Error subscribing' + result);
				} else {
					console.log(result);
				}
			});
		}
	});
	(function () {
		'use strict';
		var snackbarContainer = document.querySelector('#subToast');
		var showToastButton = document.querySelector('#subButton');
		var handler = function (event) {
			showToastButton.style.backgroundColor = '';
		};
		showToastButton.addEventListener('click', function () {
			'use strict';
			showToastButton.style.backgroundColor = '#00bcd4';
			var data = {
				message: 'Subscribed to ' + stream_key + '!',
				timeout: 20000,
				actionHandler: handler,
				actionText: 'Undo'
			};
			snackbarContainer.MaterialSnackbar.showSnackbar(data);
		});
	}());
});