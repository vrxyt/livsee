## nginx-rtmp-module front-end

Author: Joel Bethke (joel.bethke@gmail.com)

This site aims to be a easy-to-use front-end for the nginx-rtmp-module.

Current features:
  - Account System -- Lets users sign up for the site, with email verification to activate accounts. Allows for password resets. Other account features are planned (see Issues tab)
  - Private stream keys -- Each user is assigned a private streaming key. This is used to connect to the ingest server.
  - Streaming authentication -- Anyone attempting to connect without a valid account/streamkey will be denied
  - On-demand recording -- Allows anyone to start recording a stream to the server for playback later. Recorded videos are stored indefinitely. Future plans to only let the streamer start/stop their own recording.
  - On-live notifications (WIP) -- Will allow any user to subscribe to another channel and receive an email notification when they go live.
  - RESTful API -- API for grabbing stream info, possibly other functions. Useful for making widgets on another site for current viewers. Can also be used to start/stop recording if authorized.
  - Built on [MDL](https://getmdl.io/index.html)
  - Full [Sass](http://sass-lang.com/) implementation
  - Uses [video.js](https://github.com/videojs/video.js) player

Planned features:
  - See [GitHub Issues](https://github.com/Fenrirthviti/stream-site/issues) page for details or to make any feature requests.