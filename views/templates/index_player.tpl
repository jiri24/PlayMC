<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="keywords" content="hudba, doporučování hudby, algoritmus" />
        <title>{$appName}</title>
        <link rel="shortcut icon" href="{$webPath}favicon.ico" type="image/x-icon">

        <link href="{$webPath}css/bootstrap.min.css" rel="stylesheet" media="screen">
        <script src="{$webPath}js/jquery-3.2.0.min.js"></script>
        <script src="{$webPath}js/bootstrap.min.js"></script>
        <script src="{$webPath}js/popper.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--<link rel="stylesheet" href="{$webPath}css/font-awesome.min.css">-->
        <script src="https://use.fontawesome.com/aad9c7cd01.js"></script>

        <link rel="stylesheet" href="{$webPath}css/style.css">
        <script src='https://www.google.com/recaptcha/api.js'></script>

        <script src="{$webPath}polymer/components/webcomponentsjs/webcomponents-lite.js"></script>
        <link rel="import" href="{$webPath}polymer/playmc-player.html">
        <link rel="import" href="{$webPath}polymer/playmc-whitelist.html">
        <link rel="import" href="{$webPath}polymer/playmc-blacklist.html">
        <link rel="import" href="{$webPath}polymer/playmc-search.html">
        <link rel="import" href="{$webPath}polymer/playmc-artist.html">
        <link rel="import" href="{$webPath}polymer/playmc-album.html">
        <link rel="import" href="{$webPath}polymer/playmc-home.html">

        <link rel="import" href="{$webPath}polymer/components/iron-signals/iron-signals.html">

        <!-- Polymer -->
        <script src="{$webPath}polymer/components/webcomponentsjs/webcomponents-lite.js"></script>

        <link rel="import" href="{$webPath}polymer/components/app-route/app-route.html">
        <link rel="import" href="{$webPath}polymer/components/app-route/app-location.html">
        <style>
            .bg-player {
                background-color: #161616;
            }

            .outer {
                display: table;
                position: absolute;
                height: 100%;
                width: 100%;
            }

            .middle {
                display: table-cell;
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="outer">
            <div class="middle">
                <div class="container">
                    <nav class="navbar navbar-dark bg-player navbar-expand-lg">
                        <a class="navbar-brand" href="{$webPath}"><i class="fa fa-music fa-lg"></i> {$appName}</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav mr-auto">
                                <li class="nav-item">
                                    <a class="nav-link" href="#/">Doporučení</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#/white-list">Oblíbené</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#/black-list">Neoblíbené</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#/search">Vyhledávání</a>
                                </li>
                            </ul>

                            <ul class="navbar-nav ml-auto">
                                <span class="navbar-text">
                                    {$userFullName}
                                </span>
                                <li class="nav-item">
                                    <a class="nav-link" href="#/settings"><i class="fa fa-cog fa-lg"></i> Nastavení</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#/help"><i class="fa fa-question-circle fa-lg"></i> Nápověda</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#/logout"><i class="fa fa-sign-out fa-lg"></i> Odhlásit se</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <main>
                        {block name=content}
                        {/block}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>