<?php

    $code = $_GET['code'];
    $post = [
        'grant_type'    => 'authorization_code',
        'code'          => $code,
        'redirect_uri'  => 'https://miramiplaylist.mindware.com.mx/playlist.php',
    ];

    $client_id = '907c8d2ef776484897fb22f4f756abdc'; // Your client id
    $client_secret = '35195001fc1744288d16cca19ff98207'; // Your secret
    $postText = http_build_query($post);
    $ch = curl_init('https://accounts.spotify.com/api/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postText);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic '.base64_encode($client_id . ':'. $client_secret),
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);

    $access_token = $response['access_token'];
?>

<!doctype html>
<html>
  <head>
    <title>Mira Mi Playlist | Login Page</title>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>   
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> 
    <script src="src/spotify-web-api.js"></script>
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .rounded-circle{
          max-width: 6em;
        }

        .playlist_icon{
          max-width: 6em;
        }

        .title {
          margin-left: 1em !important;
          margin-bottom: 0px !important;
        }

        .description {
          margin-left: 1.5em !important;
          margin-bottom: 0px !important;
        }

        .btn {
          margin-left: 1.5em !important;
        }
    </style>
  </head>
  <body>
     <!-- ======= Header ======= -->
    <header id="header" class="fixed-top header-inner-pages">
      <div class="container d-flex align-items-center justify-content-between">

        <h1 class="logo"><a href="index.html">Mira Mi Playlist</a></h1>
        

        <nav id="navbar" class="navbar">
          <ul>
            <li><a class="nav-link scrollto active" href="index.html">Home</a></li>
            
          </ul>
          <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->

      </div>
    </header><!-- End Header -->
    <!-- ======= User section ======= -->
    <section id="services" class="services">
      <div class="container-fluid">

        <div class="section-title">
          <h1>You are logged as <span id="display_name"></span></h1>
          <img id="profile_image" class="rounded-circle"  alt="User image">
          <h3>Playlists Found</h3>
          <span class="separator"></span>
          <!-- create a span in red to show the error message -->
          <span id="error_message" class="text-danger"></span>
        </div>

        <div class="row justify-content-center">
          <div class="col-xl-10">
              <div class="row playlist_container">
                
              </div>
          </div>
        </div>
      </div>
    </section><!-- End Services Section -->
    <p id="playlist-description"></p>
    <div id="playlist-container">
      <span id="status">No Videos</span>
    </div>
    <input type="hidden" id="playlistID" value="" />
    
    <section>
    <div class="container">
      <p>Log In With Google</p>
      <button class="btn red" id="authorize-button">Log In</button>
      <button class="btn red" id="signout-button">Log Out</button>
      <br>
      <div id="content">
        <div class="row">
          <div class="col s6">
            <form id="channel-form">
              <div class="input-field col s6">
                <input type="text" id="video_search" placeholder="Pon que videos quieres ver" value="" onblur="searchnow();">
                <input type="submit" value="Get Channel Data" class="btn grey">
              </div>
            </form>
          </div>
        </div>
        <div class="row" id="video-container"></div>
      </div>
    </div>
  </section>

    <script src="https://apis.google.com/js/client.js?onload=googleApiClientReady"></script>
    <script src="main.js"></script>
    <script async defer src="https://apis.google.com/js/api.js" onload="this.onload=function(){};handleClientLoad()" onreadystatechange="if (this.readyState === 'complete') this.onload()">
    </script>
    <script src="//code.jquery.com/jquery-1.10.1.min.js"></script>
    <script>

        var playlistId;

        function searchnow(){
            var search = $('#video_search').val();
            searchVideo(search);
        }

        function execute(){
            var playlist_name = arguments[0];
            var playlist_id = arguments[1];
            var video_id = $('#video-id').val();
            var url = 'https://api.spotify.com/v1/users/'+'<?php echo $user_id; ?>'+'/playlists/'+playlist_id+'/tracks';
            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                'Authorization': 'Bearer ' + '<?php echo $access_token; ?>'
                },
                success: function(data) {
                    playlistId = createPlaylist(playlist_name,data);
                  
                    $(".playlist").hide();
                    //loop through data array and send to console
                    var items = data.items;
                    // store items in session
                    sessionStorage.setItem('items', JSON.stringify(items));
                    
                },
                error: function(data) {
                console.log(data);
                }
            });
        }

      (function() {

 
        /**
         * Obtains parameters from the hash of the URL
         * @return Object
         */
        function getHashParams() {
          var hashParams = {};
          var e, r = /([^&;=]+)=?([^&;]*)/g,
              q = window.location.hash.substring(1);
          while ( e = r.exec(q)) {
             hashParams[e[1]] = decodeURIComponent(e[2]);
          }
          return hashParams;
        }

        $.ajax({
            url: 'https://api.spotify.com/v1/me',
            type: 'GET',
            headers: {
            'Authorization': 'Bearer <?php echo $access_token; ?>',
            'Content-Type': 'application/json'
            },
            success: function(response) {
              $("#display_name").html(response.display_name);
              $("#profile_image").attr("src",response.images[0].url);
            },
            error: function(data){
                console.log(data);
            }
        });

        $.ajax({
            url: 'https://api.spotify.com/v1/me/playlists',
            type: 'GET',
            headers: {
            'Authorization': 'Bearer <?php echo $access_token; ?>',
            'Content-Type': 'application/json'
            },
            success: function(response) {
              var playlists_container = $(".playlist_container");
              $.each(response.items, function(i, item) {
                var playlist_name = item.name;
                var playlist_count = item.tracks.total;
                var playlist_image = item.images[0].url;
                var playlist_id = item.id;

                var output = '<div class="col-lg-4 col-md-6 icon-box">';
                    output+= '<div class="row">'
                    output+= '<div class="col-3"><img class="playlist_icon" src="' + playlist_image + '" alt="' + playlist_name + '"></div>';
                    output+= '<div class="col-9 align-left"><h6 class="title">' + playlist_name + '</h6>';
                    output+= '<p class="description">' + playlist_count + ' Songs</p>';
                    output+= '<button class="btn btn-secondary btn-sm" onclick="execute(\'' + playlist_name + "','" + playlist_id + '\');" style="cursor:hand;">Create Playlist</button>';
                    output+= '</div></div></div>';
                playlists_container.append(output);
              });
            },
            error: function(data){
                console.log(data);
            }
        });
    })();
    </script>
  </body>
</html>