<?php

$nav = file_get_contents("https://www.codefor.nl/nav");

$rs = [];

function showContent() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!isset($_POST["name"]) ||
            !strlen($_POST["name"]) > 0 ||
            !isset($_POST["email"]) ||
            !strlen($_POST["email"]) > 0) {
            showForm();
            showMessage("Let op: beide velden zijn verplicht.");
        } else {
            if (sendForm($_POST["name"], $_POST["email"])) {
                showMessage("Gelukt! Je ontvangt een uitnodiging per e-mail.", "success");
            } else {
                showForm();
                showMessage("Helaas... er is iets niet goed gegaan. [" . getError() . "]");
            }
        }
    } else {
        showForm();
    }
}

function showForm() {
    ?>
        <form method="post" style="display:block">
            <label>
                <span>Voornaam</span><br>
                <input type="text" name="name" <?php echo isset($_POST['name']) ? "value=\"{$_POST['name']}\"" : "" ?> >
            </label><br>

            <label style="display: block; padding-top: 1em">
                <span>E-mail</span><br>
                <input type="email" name="email" <?php echo isset($_POST['email']) ? "value=\"{$_POST['email']}\"" : "" ?> >
            </label>

            <p><input class="cta" type="submit" value="Nodig me uit!" /></p>
        </form>
    <?php
} // showForm

function showMessage($msg, $type = "error") {
    echo "<p class='message {$type}'>{$msg}</p>";
} // showMessage

function sendForm($name, $email) {
    global $rs;

    $slackInviteUrl = "https://codefornl.slack.com/api/users.admin.invite?t=" . time();

    $fields = array(
        'email' => $email,
        'first_name' => $name,
        'token' => getenv('SLACK_TOKEN'),
        'set_active' => 'true',
        '_attempts' => '1'
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $slackInviteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

    $rs = curl_exec($ch);
    $rs = json_decode($rs, true);

    curl_close($ch);

    return $rs['ok'] !== false;
}

function getError() {
    global $rs;
    return $rs['error'];
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320,initial-scale=1,user-scalable=0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="shortcut icon" sizes="128x128" href="https://codefor.nl/img/CfNL.png" />
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
        <link rel="stylesheet" href="https://www.codefor.nl/css/reset.css" type="text/css" media="all">
        <link rel="stylesheet" href="https://www.codefor.nl/css/style.css" type="text/css" media="all">
        <script src="https://use.fontawesome.com/164ce3f549.js"></script>
       <style>
            body {
                text-align: center;
                font-family: "Lato", "Verdana", "Arial", sans-serif;
                font-size: 100%;
            }
            .cta {
                display: block;
                font-size: 1.2em;
                color: white;
                background: #F7931D;
                box-shadow: 0px 3px 2px #d3d3d3;
                padding: 0.8em;
                text-decoration: none;
                border-radius: 0.4em;
                width: 10em;
                margin: 40px auto;
                text-align: center;
                line-height: 1.6em;
                border: 0;
                cursor: pointer;
            }
            .message {
                background: #808284;
                color: white;
                display: inline-block;
                padding: 6px 12px;
                border-radius: 4px;
            }
            .success {
                background: #1df7b2;
               background: #1df7b2;
            }
            .error {
                background: #f7471d;
            }
            .logo {
                width: 80px;
            }
            .logo img {
                width: 68px;
            }
            .logo h1 {
                font-size: 0.8em;
            }
            span {
                line-height: 2em;
            }
            input {
                border: 1px solid silver;
                padding: 0.2em;
                font-size: 1em;
            }
        </style>
    </head>
    <body>
        <?=$nav?>
        <header>
            <div class="wrapper">
                <h2>Praat mee op onze Slack<br>en doe mee!<br><a href="https://codefornl.slack.com">codefornl.slack.com</a></h2>
                <section class="slack"><?php showContent() ?></section>
                <a href="https://www.codefor.nl" class="logo">
                    <img alt="</" src="https://codefor.nl/img/larger_CfNL.png">
                    <h1>Code for NL</h1>
                </a>
            </div>
        </header>
        <main style="margin-bottom: 40px">
        </main>
    </body>
</html>

