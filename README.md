## Rachni - an nginx-rtmp-module front-end

Author: Joel Bethke (joel.bethke@gmail.com)

This site aims to be a easy-to-use front-end for the nginx-rtmp-module.

Current features:
  - Account System -- Lets users sign up for the site, with email verification to activate accounts. Allows for password resets. Other account features are planned (see GitHub Issues)
  - Private stream keys -- Each user is assigned a private streaming key. This is used to connect to the ingest server. Anyone attempting to connect without a valid account/streamkey will be denied
  - On-demand recording -- Allows anyone to start recording a stream to the server for playback later. Recorded videos are stored indefinitely. Future plans to only let the streamer start/stop their own recording.
  - In-site recording playback -- All recorded videos are viewable/playable from the site itself. Download is allowed.
  - On-live notifications -- Will allow any user to subscribe to another channel and receive an email notification when they go live. Currently implemented simply, future enhancements planned.
  - Stream API -- API for grabbing stream info, possibly other functions. Useful for making widgets on another site for current viewers/live channels. Can also be used to start/stop recording if authorized.
  - Built on [MDL](https://getmdl.io/index.html)
  - [Sass](http://sass-lang.com/)
  - [video.js](https://github.com/videojs/video.js) player

Planned features:
  - See [GitHub Issues](https://github.com/Fenrirthviti/stream-site/issues) page for details or to make any feature requests.

Config information:
  - This site uses (and requires): 
    - NGINX with nginx-rtmp-module, http-ssl-module, and http_xslt_module
    - postgresql
    - PHP7 (I believe it should run on PHP5.4+, but I have not tested)
    - JavaScript
  - This is the config string I used for nginx:

  `--prefix=/etc/nginx --user=nginx --group=nginx --sbin-path=/usr/sbin/nginx --conf-path=/etc/nginx/nginx.conf --error-log-path=/var/log/nginx/error.log --http-log-path=/var/log/nginx/access.log --pid-path=/var/run/nginx.pid --lock-path=/var/run/nginx.lock --with-http_ssl_module --with-ipv6 --add-module=/home/streaming/nginx-build/nginx-rtmp-module-1.1.7 --with-http_xslt_module --with-openssl=/home/streaming/openssl-build/openssl-1.0.2g`

  - All nginx conf files can be found in /src/nginx
    - NOTE: This is a fairly complex nginx setup. Please make sure you read all the conf files and make the necessary directory/config changes to them

  - NOTE: Many of the administration features will require manual database manipulation currently. There is not much that will need to be done, but be aware there is no admin console currently. It is planned for future updates, but has not been a priority.

Installtion:
  - Install nginx with nginx-rtmp-module, http-ssl-module, and http_xslt_module (see above).
    - Verify all config files are updated to the paths you want to use. Check every file, there is a lot to configure.
  - Install pgsql and set up your database user.
  - Import the sql files from /src/pgsql to your database. This will set up the two required tables.
    - Make sure you update line 18 in subscribers.sql and line 23 of users.sql to your database user account.
  - Edit /lib/database.class.php with your DB info
  - Edit /inc/config.php to your liking
  - Copy everything but /src and /scss to your server. 
    - If you wish to use Sass to edit the site layouts/colors, all the files are in /src/css and the main file is /scss/application.css
    - Otherwise, just copy /css to use the pre-compiled versions. /css/site.css is required either way.
  - Probably lots of other stuff I'm forgetting.
