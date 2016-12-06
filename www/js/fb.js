
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/de_DE/sdk.js#xfbml=1&version=v2.8&appId=358601767847484";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'))

 // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
     // posten();
      testAPI();
    } else if (response.status === 'not_authorized') {
      // The person is logged into Facebook, but not your app.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';
    } else {
      // The person is not logged into Facebook, so we're not sure if
      // they are logged into this app or not.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into Facebook.';
    }
  }

  function posten(){
    FB.login(function(){
      // Note: The call will only work if you accept the permission request
      FB.api('/me/feed', 'post', {message: 'Tue Gutes für dein Umfeld!'});
    }, {scope: 'publish_actions'});
  }

  // Aufruf fürs Teilen:
  function teilen(){
    FB.ui({
      method: 'share_open_graph',
      action_type: 'og.likes',
      action_properties: JSON.stringify({object:'https://developers.facebook.com/docs/',
      })
    }, function(response){
      // Debug response (optional)
      console.log(response);
    });
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

    // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.

  
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', {fields: 'first_name, last_name, email'}, function(response) {
      console.log(response);
      var request = new XMLHttpRequest();
      request.open('post', 'loginWithFB.php', true);
      request.setRequestHeader('Content-Type', 'application/x-www-formurlencoded');
      request.send('id='+response.id);
      request.send('user='+response.name);
      request.send('vorname='+response.first_name);
      request.send('nachname='+response.last_name);
      request.send('email='+response.email);

      document.getElementById('status').innerHTML = response.first_name+' '+response.last_name+' '+response.email;
      document.location.href = "loginWithFB.php";  // <<< änderung der aktuellen Seite auf gegebene
    });
      
  }
  
  

