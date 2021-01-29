<?php
// includes site vars
include 'config.php';
?>
<!doctype html>
<html>
<head>
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?= $sitetitle ?> Weryfikacja konta</title>
</head>
<body
	style="font-family: Arial, sans-serif;line-height: 1.6em;font-size: 100%;background-color: rgb(43, 43, 43);color: rgb(255, 255, 255);-webkit-font-smoothing: antialiased;height: 100%;-webkit-text-size-adjust: none;margin: 0;padding: 0;width: 100% !important;">
<table class="body-wrap" style="padding: 20px;width: 100%;">
	<tr>
		<td></td>
		<td class="container" bgcolor="#4e4e4e"
			style="border-radius: 5px;padding: 20px;border: 1px solid rgb(240, 240, 240);clear: both !important;display: block !important;margin: 0 auto !important;max-width: 600px !important;">
			<div class="content" style="display: block;margin: 0 auto;max-width: 600px;">
				<table style="width: 100%;">
					<tr>
						<td>
							<h1 style="color: rgb(255, 255, 255);font-family: Arial, sans-serif;font-weight: 200;line-height: 1.2em;margin: 20px 0 10px;font-size: 36px;"><?= $sitetitle ?>
								Weryfikacja konta</h1>
							<p style="font-size: 14px;font-weight: normal;margin-bottom: 10px;">Witamy cię <?= $displayname ?>,</p>
							<p style="font-size: 14px;font-weight: normal;margin-bottom: 10px;">Dziękujemy za
								rejestracje na <?= $sitesubtitle ?>! Zobacz niżej instrukcje
								jak aktywować konto:</p>
							<table class="btn-primary" cellpadding="0" cellspacing="0" border="0"
								   style="margin: 20px auto;width: 100% !important;">
								<tr>
									<td style="border-radius: 25px;font-family: Arial, sans-serif;font-size: 14px;text-align: center;vertical-align: top;">

										<a href="<?= $furl ?>/login/verify/<?= $email ?>/<?= $authcode ?>"
										   style="color: rgb(255, 255, 255)!important;padding: 12px 24px;font-size: 29px;line-height: 1.3333333;border-radius: 3px;display: inline-block;margin: auto;font-weight: normal;text-align: center;vertical-align: middle;touch-action: manipulation;cursor: pointer;background: rgb(0, 186, 213) none;border: 1px solid transparent;white-space: nowrap;-webkit-user-select: none;text-decoration: none;">Weryfikuj
										konto</a>

									</td>
								</tr>
							</table>
							<h1 style="color: rgb(255, 255, 255);font-family: Arial, sans-serif;font-weight: 200;line-height: 1.2em;margin: 20px 0 10px; text-align: center;font-size: 36px;">
								Naciśnij niebieski guzik</h1>
							<p style="font-size: 14px;font-weight: normal;margin-bottom: 10px;">To nie jest trudne!</p>
							<h2 style="color: rgb(255, 255, 255);font-family: Arial, sans-serif;font-weight: 200;line-height: 1.2em;margin: 20px 0 10px;font-size: 28px;">
								FAQ:</h2>
							<p style="font-size: 14px;font-weight: normal;margin: 10px 25px 0;"><b>Q:</b> Jak mam to nacisnąć?</p>
							<p style="font-size: 14px;font-weight: normal;margin: 5px 25px 0;"><b>A:</b> Najedź kursorem na niebieski przycisk
								na którym pisze "Weryfikuj konto" po czym naciśnij lewy przycisk myszy.
							</p>

							<p style="font-size: 14px;font-weight: normal;margin-bottom: 10px;">Czy już nacisłeś?
								Nie? To dlaczego tego nie robisz!</p>
							<p style="font-size: 14px;font-weight: normal;margin-bottom: 10px;">Jeżeli nacisłeś,
								wszystko powinno być gotowe!</p>
							<p style="font-size: 14px;font-weight: normal;margin-bottom: 10px;">Jeżeli nacisłeś,
								i masz jakiś problem pisz na naszego maila:</p>
							<p class="text-center"
							   style="font-size: 14px;font-weight: bold;margin-bottom: 10px;text-align: center;"><a
									href="mailto:<?= $reply_email ?>"
									style="color: rgb(0, 186, 213);"><?= $reply_email ?></a></p>
						</td>
					</tr>
				</table>
			</div>
		</td>
		<td></td>
	</tr>
</table>
<table class="footer-wrap" style="width: 100%;clear: both !important;">
	<tr>
		<td></td>
		<td class="container"
			style="border-radius: 5px;clear: both !important;display: block !important;margin: 0 auto !important;max-width: 600px !important;">
			<div class="content" style="display: block;margin: 0 auto;max-width: 600px;">
				<table style="width: 100%;">
					<tr>
						<td class="text-center" align="center" style="text-align: center;">
							<p style="font-size: 12px;font-weight: normal;margin-bottom: 10px;color: rgb(255, 255, 255);">
								Pytania? Pomysły? Problemy? <a href="mailto:fenrir@rirnef.net"
																	   style="color: rgb(0, 186, 213);">Daj nam znać</a>.
							</p>
						</td>
					</tr>
				</table>
			</div>
		</td>
		<td></td>
	</tr>
</table>
</body>
</html>
