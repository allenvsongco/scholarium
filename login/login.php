<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}

if( !empty($_POST) ) {
    $creds['user'] = stripslashes(trim($_POST['user']));
    $creds['pass'] = stripslashes(trim($_POST['pass']));

    $token = authAPI($creds);

    $_SESSION['token'] = null;

    if($token) {
        $_SESSION['token'] = $token;
        header('Location:/');

    } else {
        $_SESSION['bad_login'] = true;
        header('Location:' . $_SERVER['HTTP_REFERER']);
    }

}

function authAPI($post) {
    $headers = array(
        'Authorization: Basic ' . base64_encode('TMTG' . ":" . 'tujyBpbgtum3xcctFvXZgr4ZnaRsddVRpvkwJuq8B3KEwfd4BZQtrRaj5r4vdtDm')
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://scholarium.tmtg-clone.click/api/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_HTTPHEADER => $headers
    ));

    //Execute the cURL request. Convert response to json
    $response = json_decode(curl_exec($curl), true);

    //Check if any errors occured.
    if (curl_errno($curl)) {
        // throw the an Exception.
        throw new Exception(curl_error($curl));
    }

    curl_close($curl);

    //get the response.
    if(array_key_exists('token', $response['data'][0])) {
        return $response['data'][0]['token'];
    } else {
        return false;
    }
}

?>