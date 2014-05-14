<?php

require 'src/facebook.php';

$facebook = new Facebook(array(
    'appId'  => '544223315676947',
    'secret' => '5c55265a98ce9c7b734b6ad072427ba8',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
    try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me');
    } catch (FacebookApiException $e) {
        echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
        $user = null;
    }
}

?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<body>
<div style="padding:10px;height:200px;background-color: greenyellow;">

    <div id="fb-root"></div>
    <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=544223315676947";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>

</div>

<?php if ($user) { ?>
    Your user profile is
    <pre>
        <?php echo utf8_decode($user_profile['name']),"\n" ; ?>
        <?php print htmlspecialchars(print_r($user_profile, true)) ?>
      </pre>
<?php } else { ?>
    <fb:login-button></fb:login-button>
<?php } ?>
<div id="fb-root"></div>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId: '<?php echo $facebook->getAppID() ?>',
            cookie: true,
            xfbml: true,
            oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
            window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
            window.location.reload();
        });
    };
    (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
            '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
    }());
</script>
</body>
</html>