<script>
        //https://stackoverflow.com/a/15306649

        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var ScriptTag = document.getElementsByTagName('script')[0];
        ScriptTag.parentNode.insertBefore(tag, ScriptTag);

        /**
         * Put your video IDs in this array
         */
        var videoIDs = [
        	/*
            'YtF6p_w-cSc',
            'mGPigZiiMvk',
            'Wg9OLZ-5BRM'
            */
            [arryVideoIDs]
        ];

        var player, currentVideoId = 0;

        function onYouTubeIframeAPIReady() {
            player = new YT.Player('player', {
                height: '350',
                width: '425',
                playerVars: {
                    rel: 0  //disable related videos
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            //event.target.loadVideoById(videoIDs[currentVideoId]);		//autoplay
            //event.target.cueVideoById(videoIDs[currentVideoId]);		//no autoplay
            event.target.[autoplay](videoIDs[currentVideoId]);
        }

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.ENDED) {
                //console.log('ended: ' + currentVideoId);

                currentVideoId++;
                if (currentVideoId < videoIDs.length) {
                    setActiveThumb(videoIDs[currentVideoId]);

                    player.loadVideoById(videoIDs[currentVideoId]);
                }
            }
        }


        function playVideo($youtubeID) {
            //console.log($youtubeID);
            //player.pauseVideo();
            setActiveThumb($youtubeID);
            player.loadVideoById($youtubeID);
            currentVideoId = videoIDs.indexOf($youtubeID);

            //console.log(currentVideoId);
        }


        function setActiveThumb($activeThumbID) {
            //loop through all images with "thumb" class, and remove the "active" class
            var elems = document.querySelectorAll(".thumb");
            [].forEach.call(elems, function (el) {
                el.classList.remove("active");
            });

            //add the "active" class to the current image
            document.getElementById($activeThumbID).classList.add('active');
        }
    </script>