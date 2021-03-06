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

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--<link rel="stylesheet" href="{$webPath}css/font-awesome.min.css">-->
        <script src="https://use.fontawesome.com/aad9c7cd01.js"></script>

        <link rel="stylesheet" href="{$webPath}css/style.css">
        <script src='https://www.google.com/recaptcha/api.js'></script>

        <script src="{$webPath}js/vue.js"></script>
        <script src="{$webPath}js/vue-router.js"></script>
    </head>
    <body>
        <div id="app">
            <header>
            </header>

            <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand" href="{$webPath}"><i class="fa fa-music fa-lg"></i> {$appName}</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            {if !$userLogin}
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Domů</a>
                                </li>
                            {else}

                            {/if}
                        </ul>
                        <ul class="navbar-nav ml-auto">
                            {if !$userLogin}
                                <li class="nav-item"><a class="nav-link" href="{$webPath}index.php?registration&register"><i class="fa fa-pencil fa-lg"></i> Zaregistrovat se</a></li>
                                <li class="nav-item"><a class="nav-link" href="{$webPath}index.php?account&login"><i class="fa fa-sign-in fa-lg"></i> Přihlásit se</a></li>
                                {else}
                                <li class="nav-item dropdown">
                                    <a class="nav-link" href="{$webPath}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{$userFullName} <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{$webPath}index.php?account&settings"><i class="fa fa-id-card fa-lg"></i> Nastavení</a></li>
                                        <li><a href="{$webPath}index.php?account&logout"><i class="fa fa-sign-out fa-lg"></i> Odhlásit se</a></li>
                                    </ul>
                                </li>
                            {/if}
                        </ul>
                    </div>
                </div>
            </nav>

            <main>       
                {block name=content}
                {/block}
            </main>

            <footer class="footer">
                <p class="p-to-center">© 2017 Jiří Valůšek</p>
            </footer>
        </div>
    </body>
</html>