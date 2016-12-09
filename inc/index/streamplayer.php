<div class="mdl-grid mdl-grid--no-spacing fill">
    <div class="mdl-cell mdl-cell--9-col fill" id="videoBox">
        <div class="live-player">
            <video class="video-js vjs-default-skin vjs-fill vjs-big-play-centered"
                   data-setup='{"aspectRatio":"16:9","controls": true, "autoplay": true, "preload": "auto"}'
                   id="streamPlayer"
                   width="100%" height="100%"
                   poster="//<?= $surl ?>/img/channel/channel_<?= $streamkey ?>.png"
            >
                <source src="rtmp://<?= $surl ?>/live/<?= $streamkey ?>" type="rtmp/flv" label='Flash'/>
                <source src="//<?= $surl ?>/hls/<?= $streamkey ?>.m3u8" type="application/x-mpegurl" label='HLS'/>
            </video>
        </div>
    </div>
    <div class="mdl-cell mdl-cell--3-col mdl-card mdl-shadow--2dp employer-form" id="channelchat">
        <div class="mdl-card__title">
            <span class="mdl-color-text--cyan-500">Channel Chat</span>
        </div>

        <div class="mdl-card__supporting-text full-height">
            <div id="output" style="height: 100%;overflow-y: scroll;">

            </div>
        </div>


        <form action="" method="POST" class="form" id="chatMessage">

            <div class="chatinput" style="position:relative;">

                <div class="mdl-grid--no-spacing chatbox">
                    <div class="mdl-cell mdl-cell--12-col mdl-textfield mdl-js-textfield mdl-textfield--floating-label">

                        <textarea class="mdl-textfield__input" type="text" name="inputMessage" id="inputMessage"
                                  rows="1" autocomplete="off"/></textarea>

                        <label class="mdl-textfield__label" for="inputMessage">Enter message</label>
                    </div>
                    <button type="submit" form="chatMessage"
                            class="mdl-button mdl-js-button mdl-button--icon sendbutton">
                        <i class="material-icons sendbuttonicon">send</i>
                    </button>

                </div>
            </div>

        </form>
    </div>
</div>