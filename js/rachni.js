/**
 * Created by Joel on 10/27/2016.
 */
if (window.jQuery) {
	$(function () {
		let pauseHeartbeat = false;
		let heartbeatXHR;
		let recIconName = $('.record-button').eq(0).next('i').text();
		let toggled = false;
		let scrolledPct = 0;
		let server = 'wss://rachni.com:9090/';

		// Function to write received messages
		function write_to_chatbox(message_json) {
			let chatbox = $('#output .mCSB_container');
			let unixTimeStamp = message_json['timestamp'];
			let timestampInMilliSeconds = unixTimeStamp * 1000;
			let date = new Date(timestampInMilliSeconds);
			let formattedDate = date.format('h:i a');

			// custom style differences for SYSTEM and USER messages
			if (message_json['type'] === 'SYSTEM') {
				// will need to change this to either hide system messages, or add an additional check for join/parts
				if (jp_status === 't') {
					chatbox.append("<span style='color: rgb(179, 179, 179);'>(" + formattedDate + ')<span style="font-style: italic"> ' + message_json['message'] + '</span></span><br/>');

				}
			} else {
				chatbox.append('(' + formattedDate + ") <span style='color: rgb(0, 188, 212); font-weight: bold'>" + message_json['user'] + ':</span> ' + urlify(message_json['message']) + '<br/>');

			}

			// makes sure that the chat window stays scrolled to the bottom when a new message is sent
			if (scrolledPct === 100) {
				$('#output').mCustomScrollbar('scrollTo', 'bottom');
			}
		}

		// Simple regex for identifying URLs in the chat.
		function urlify(text) {
			let regex = /(https?:\/\/[^\s]+)/g;
			return text.replace(regex, '<a href="$1" target="_blank">$1</a>')
		}

		// Join request sent on page load, but only if chat is present on the page.
		if (ischat === true) {

			let socket = new WebSocket(server);

			socket.onerror = function (error) {
				console.log('WebSocket Error:');
				console.log(error);
			};

			socket.onopen = function (event) {
				console.log('socket open');
				let data = JSON.stringify({
					"message": display_name + " has joined.",
					"timestamp": Math.round(new Date().getTime() / 1000),
					"user": display_name,
					"channel": current_channel,
					"type": "SYSTEM"
				});
				socket.send(data);
				$('#inputMessage').focus();
			};

			socket.onmessage = function (event) {
				console.log('socket message');
				write_to_chatbox(JSON.parse(event.data));
			};

			socket.onclose = function (event) {
				console.log('socket close');
				let data = {
					"message": "Disconnected from server!",
					"timestamp": Math.round(new Date().getTime() / 1000),
					"user": "System",
					"channel": current_channel,
					"type": "SYSTEM"
				};
				write_to_chatbox(data);
			};

			// Chatbox show/hide
			$("#toggleChat").click(function () {
				if (toggled) {
					$('.chat-container').stop().animate({'width': '20%'}, 500);
					$('.video-container').stop().animate({'width': '80%'}, 500);
					toggled = false;
				} else {
					$('.chat-container').stop().animate({'width': '0%'}, 500);
					$('.video-container').stop().animate({'width': '100%'}, 500);
					toggled = true;
				}
			});

			let data2 = {
				"message": "Attempting to connect...",
				"timestamp": Math.round(new Date().getTime() / 1000),
				"user": "System",
				"channel": current_channel,
				"type": "SYSTEM"
			};
			write_to_chatbox(data2);

		}

		// If current channel is set, we can expect to need these functions
		if (typeof current_channel === "undefined") {
			console.log('Heartbeats not run.');
		} else {

			// Set recording button status, and refreshes page if stream was offline and is now live
			setInterval(function () {
				if (!pauseHeartbeat) {
					heartbeatXHR = $.getJSON('/api/' + api_key + '/stream/ping', function (info) {
						if (typeof stream_key != "undefined") {
							if (typeof info[stream_key] !== 'undefined' && info[stream_key].active === true && live_status === false) {
								console.log("We're live!");
								resetPlayer();
							}
							if (typeof info[stream_key] === 'undefined' && live_status === true) {
								console.log("Channel offline.");
								live_status = false;
								$('.vjs-error-display').hide();

								$('.vjs-poster').css({
									'background-image': 'url(' + videoposter + ')',
									'display': 'block'
								});
								$('.vjs-paused .vjs-big-play-button').css({'display': 'none'});
							}
						}

						$('.record-button').each(function () {
							let channel = $(this).parent('label').parent('td').parent('tr').attr('channel');
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
			}, 2000);
		}

		// Capture the input box so that enter submits a message instead of newline, but still allow for shift+enter
		$("#inputMessage").keypress(function (e) {
			if (e.which === 13 && !e.shiftKey) {
				$(this).closest("form").submit();
				e.preventDefault();
				return false;

			}
		});

		// Submit message to chat server
		$('#chatMessage').submit(function (event) {
			let data = {
				'message': $('#inputMessage').val(),
				'timestamp': Math.round(new Date().getTime() / 1000),
				'user': display_name,
				'channel': current_channel,
				'type': 'USER'
			};
			data = JSON.stringify(data);
			event.preventDefault();
			socket.send(data);
			$('#inputMessage').val("").parent('div').removeClass('is-dirty');
		});

		/** CHANNEL FUNCTIONS **/

		// Set recording button status, and refreshes page if stream was offline and is now live
		if (typeof api_key === "undefined") {
			console.log('Heartbeat not run');
		} else {

		}

		// Recording start/stop on icon button click
		$('.record-button').click(function () {
			let channel = $(this).parent('label').parent('td').parent('tr').attr('channel');
			let icon = $(this).next('i');
			pauseHeartbeat = true;
			heartbeatXHR.abort();
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

		// Subscribe button functions
		$('#subButton').click(function () {
			let snackbarContainer = document.querySelector('#subToast');
			let button = $(this);
			let channel = $(this).attr('channel');
			let action = $(this).text();
			'use strict';
			if (action === 'Unsubscribe') {
				console.log('Action: Unsubscribe');
				$.getJSON('/api/' + api_key + '/subscription/remove/' + channel, function (result) {
					if (result === false) {
						console.log('Error unsubscribing');
					} else {
						const data = {
							message: 'Unsubscribed from ' + stream_key + '.',
							timeout: 5000
						};
						console.log(result);
						button.text('Subscribe');
						button.css('background-color', '');
						snackbarContainer.MaterialSnackbar.showSnackbar(data);
					}
				});
			} else if (action === 'Subscribe') {
				console.log('Action: Subscribe');
				$.getJSON('/api/' + api_key + '/subscription/add/' + channel, function (result) {
					if (result === false) {
						const data = {
							message: 'Error subscribing (probably already subscribed)!',
							timeout: 5000
						};
						console.log('Error subscribing' + result);
						snackbarContainer.MaterialSnackbar.showSnackbar(data);
					} else {
						const data = {
							message: 'Subscribed to ' + stream_key + '!',
							timeout: 5000
						};
						console.log(result);
						button.text('Unsubscribe');
						button.css('background-color', '#00bcd4');
						snackbarContainer.MaterialSnackbar.showSnackbar(data);
					}
				});
			}
		});

		// Link copy toast
		$('#copyButton').click(function () {
			let snackbarContainer = document.querySelector('#keyCopy');
			const data = {
				message: 'Stream key copied!',
				timeout: 5000
			};
			snackbarContainer.MaterialSnackbar.showSnackbar(data);
		});

		// File upload name update workaround
		$('#avatar').change(function () {
			document.getElementById("avatar_file").value = this.files[0].name;
		});
		$('#offline').change(function () {
			document.getElementById("offline_file").value = this.files[0].name;
		});

		if (window.mCustomScrollbar) {
			// Custom scrollbar initializations
			$(window).load(function () {
				$(".scrollContent").mCustomScrollbar({
					theme: "inset",
					scrollInertia: 400,
					scrollButtons: {enable: true}
				});
			});
			$("#output").mCustomScrollbar({
				theme: "inset",
				scrollInertia: 400,
				callbacks: {
					onInit: function () {
						$("#output").mCustomScrollbar('scrollTo', 'bottom');
					},
					whileScrolling: function () {
						scrolledPct = this.mcs.topPct;
					},
				}
			});
		} else {
			console.log('No mCustomScrollbar found! Please load it first.');
		}
	});

} else {
	console.log('No jQuery found! Please load it first.');
}

