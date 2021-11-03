const redirec_uri = 'https://miramiplaylist.mindware.com.mx/playlist.html';
const client_id = '907c8d2ef776484897fb22f4f756abdc';
const client_secret = '35195001fc1744288d16cca19ff98207';

const authorize = 'https://accounts.spotify.com/authorize';

function onPageLoad(){
    if(window.location.search.length > 0){
        var url = window.location.search;
        var code = url.split('=')[1];
        var data = {
            'grant_type': 'authorization_code',
            'code': code,
            'redirect_uri': redirec_uri,
            'client_id': client_id,
            'client_secret': client_secret
        };
        $.ajax({
            url: 'https://accounts.spotify.com/api/token',
            type: 'POST',
            data: data,
            success: function(data){
                localStorage.setItem('access_token', data.access_token);
                window.location.href = 'https://miramiplaylist.mindware.com.mx/playlist.html';
            },
            error: function(data){
                console.log(data);
            }
        });
    }
}

function requestAuthorization(){
    var url = authorize + '?client_id=' + client_id + '&response_type=code&redirect_uri=' + redirec_uri + '&scope=user-read-private%20user-read-email&show_dialog=true';
    window.location.href = url;
}