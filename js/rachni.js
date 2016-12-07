/**
 * Created by Joel on 10/27/2016.
 */

$(function () {

	//test chatbox show/hide
	$("#toggleChat").click(function () {
		$("#channelchat").animate({width: 'toggle'});
		$("#videoBox").toggleClass('mdl-cell--9-col mdl-cell--12-col', 1000, "easeInOutQuad", function () {
			$('#output').scrollTop($('#output').prop('scrollHeight'));
		});
	});

	/** CHAT FUNCTIONS **/

	// Join
	$.ajax({
		url: "/api/" + api_key + "/chat/join/" + current_channel,
		dataType: 'json'
	});

	// Leave
	$(window).on('beforeunload', function () {
		$.ajax({
			url: "/api/" + api_key + "/chat/leave/" + current_channel,
			dataType: 'json'
		});
	});

	var chatbox = $('#output');
	var inputform = $('#chatMessage');
	var lastid = 0;

	function urlify(text) {
		var regex = /(https?:\/\/[^\s]+)/g;
		return text.replace(regex, '<a href="$1">$1</a>')
	}

	setInterval(function () {
			$.ajax({
				url: "/api/" + api_key + "/chat/read/" + current_channel,
				dataType: 'json'
			}).done(function (getLines) {
				var scrollHeight = chatbox.prop('scrollHeight') - chatbox.height();
				$.each(getLines, function (id, line) {
					var id = parseInt(line.id, 10);
					var type = line.type;
					if (lastid < id) {
						lastid = id;
						var unixTimeStamp = line.timestamp;
						var timestampInMilliSeconds = unixTimeStamp * 1000;
						var date = new Date(timestampInMilliSeconds);
						var formattedDate = date.format('h:i a');
						if (type === 'SYSTEM') {
							if (jp_status === 't') {
								chatbox.append(" <span style='color: rgb(179, 179, 179);'>(" + formattedDate + ')<span style="font-style: italic"> ' + line.sender + ' ' + urlify(line.message) + '</span></span><br />');
							}
						} else {
							chatbox.append('(' + formattedDate + ") <span style='color: rgb(0, 188, 212); font-weight: bold'>" + line.sender + ':</span> ' + urlify(line.message) + '<br />');
						}
					}
				});
				if (scrollHeight === 0 || chatbox.scrollTop() === scrollHeight) {
					chatbox.scrollTop(chatbox.prop('scrollHeight'));
				}
			});
		},
		500
	);
	$("#inputMessage").keypress(function (e) {
		if (e.which == 13 && !e.shiftKey) {
			$(this).closest("form").submit();
			e.preventDefault();
			return false;
		}
	});
	inputform.submit(function (event) {
		event.preventDefault();
		var data = {
			'message': $('#inputMessage').val(),
			'timestamp': Date.now() / 1000 || 0,
			'user': display_name,
			'channel': current_channel
		};
		$.ajax({
			type: "POST",
			url: "/api/" + api_key + "/chat/write/",
			data: data,
			dataType: 'json'
		});
		$('#inputMessage').val("").parent('div').removeClass('is-dirty');
	});


	/** CHANNEL FUNCTIONS **/

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

// sub button functions
	'use strict';
	var snackbarContainer = document.querySelector('#subToast');
	$('#subButton').click(function() {
		var button = $(this);
		var channel = $(this).attr('channel');
		var action = $(this).text();
		'use strict';
		if (action === 'Unsubscribe') {
			console.log('Action = Unsubscribe');
			$.getJSON('/api/' + api_key + '/subscription/remove/' + channel, function (result) {
				if (result === false) {
					console.log('Error unsubscribing');
				} else {
					console.log(result);
					button.text('Subscribe');
					button.css('background-color', '');
					var data = {
						message: 'Unsubscribed from ' + stream_key + '.',
						timeout: 5000
					};
					snackbarContainer.MaterialSnackbar.showSnackbar(data);
				}
			});
		} else if (action === 'Subscribe') {
			console.log('Action = Subscribe');
			$.getJSON('/api/' + api_key + '/subscription/add/' + channel, function (result) {
				if (result === false) {
					console.log('Error subscribing' + result);
					var data = {
						message: 'Error subscribing (probably already subscribed)!',
						timeout: 5000
					};
					snackbarContainer.MaterialSnackbar.showSnackbar(data);
				} else {
					console.log(result);
					button.text('Unsubscribe');
					button.css('background-color', '#00bcd4');
					var data = {
						message: 'Subscribed to ' + stream_key + '!',
						timeout: 5000
					};
					snackbarContainer.MaterialSnackbar.showSnackbar(data);
				}
			});
		}
	});
});


