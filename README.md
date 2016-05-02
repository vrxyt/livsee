## nginx-rtmp-module front-end

This site aims to be a easy-to-use front-end for the nginx-rtmp-module.

Current features:
  - Account System -- Lets users sign up for the site, with email verification to activate accounts. Allows for password resets. Other account features are planned (see Issues tab)
  - Private stream keys -- Each user is assigned a private streaming key. This is used to connect to the ingest server.
  - Streaming authentication -- Anyone attempting to connect without a valid account/streamkey will be denied
  - On-demand recording (WIP) -- Allows anyone to start recording a stream to the server for playback later. Recorded videos are stored indefinitely.
  - Built on [MDL](https://getmdl.io/index.html)
  - Full [Sass](http://sass-lang.com/) implementation
  - Uses [video.js](https://github.com/videojs/video.js) player
