<script>

	//code if thumbnail isn't clicked
	//var nextVideo = ["../videos/first.mp4","../videos/second.mp4","../videos/third.mp4", "../videos/first.mp4"];
	var nextVideo = [videoArray];
	var curVideo = 0;
	var videoPlayer = document.getElementById('videoPlayer');

	videoPlayer.onended = function(){
		++curVideo; //comment out for infinite loop

		if(curVideo < nextVideo.length-1){ //comment out for infinite loop
			videoPlayer.src = nextVideo[curVideo];   

			//extract the name of the thumbnail's ID 
			$img = extractThumbID(nextVideo[curVideo]);

    		console.log(curVideo + ": " + nextVideo[curVideo] + " | img = " + $img);     
			
			//add the "active" class to the current image
			setActiveThumb($img);

			if (curVideo >= 1){
				videoPlayer.play();
			}
			//uncomment for infinite loop
			/*
			if (curVideo == nextVideo.length-1){
				curVideo = 1;
			} else {
				++curVideo;
			}
			*/

		} //comment out for infinite loop
	}

		
	function playVideo($video, $id){
		console.log("thumbnail clicked = " + $video);

		//set the class of the just clicked thumbnail to "active"
		$img = extractThumbID($video);
		setActiveThumb($id);

		//var nextVideo = ["../videos/first.mp4","../videos/second.mp4","../videos/third.mp4", "../videos/first.mp4"];
		var nextVideo = [videoArray];
		
		switch($video) {
[videoSwitch]
			/*
			case "../videos/first.mp4":
				curVideo = 0;
				break;
			case "../videos/second.mp4":
				curVideo = 1;
				break;
			case "../videos/third.mp4":
				curVideo = 2;
				break;
			*/
			default:
				curVideo = 0;
		}

		var videoPlayer = document.getElementById('videoPlayer');
		videoPlayer.src = $video;   
		//videoPlayer.play();
		videoPlayer.onended = function(){
			++curVideo;
			if(curVideo < nextVideo.length-1){    		
				videoPlayer.src = nextVideo[curVideo];   

				//extract the name of the thumbnail's ID 
				$img = extractThumbID(nextVideo[curVideo]);

				console.log("from thumbnail: " + nextVideo[curVideo] + " | img = " + $img);  

				//add the "active" class to the current image
				setActiveThumb($img);      
			} 
		}
	}


	function extractThumbID($val){
        // Use the regular expression to replace the non-matching content with a blank space
        var filenameWithExtension = $val.replace(/^.*[\\\/]/, '');

        return filenameWithExtension;
	}


	function setActiveThumb($activeThumbID){
		//loop through all images with "thumb" class, and remove the "active" class
		var elems = document.querySelectorAll(".thumb");
		[].forEach.call(elems, function(el) {
			el.classList.remove("active");
		});

		//add the "active" class to the current image
		document.getElementById($activeThumbID).classList.add('active');
	}
		
</script>