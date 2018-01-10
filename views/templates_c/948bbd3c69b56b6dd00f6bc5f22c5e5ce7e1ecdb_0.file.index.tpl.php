<?php
/* Smarty version 3.1.30, created on 2017-10-18 13:43:06
  from "C:\xampp\htdocs\PlayMC\views\templates\index.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_59e73e4a70bb71_63745935',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '948bbd3c69b56b6dd00f6bc5f22c5e5ce7e1ecdb' => 
    array (
      0 => 'C:\\xampp\\htdocs\\PlayMC\\views\\templates\\index.tpl',
      1 => 1508326985,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59e73e4a70bb71_63745935 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="keywords" content="hudba, doporučování hudby, algoritmus" />
        <title><?php echo $_smarty_tpl->tpl_vars['appName']->value;?>
</title>

        <link href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
css/bootstrap.min.css" rel="stylesheet" media="screen">
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
js/jquery-3.2.0.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
js/bootstrap.min.js"><?php echo '</script'; ?>
>

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
css/font-awesome.min.css">-->
        <?php echo '<script'; ?>
 src="https://use.fontawesome.com/aad9c7cd01.js"><?php echo '</script'; ?>
>

        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
css/style.css">
        <?php echo '<script'; ?>
 src='https://www.google.com/recaptcha/api.js'><?php echo '</script'; ?>
>
    </head>
    <body>
        <header>
        </header>

        <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
"><i class="fa fa-music fa-lg"></i> <?php echo $_smarty_tpl->tpl_vars['appName']->value;?>
</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Domů</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <?php if (!$_smarty_tpl->tpl_vars['userLogin']->value) {?>
                            <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
index.php?registration&register"><i class="fa fa-pencil fa-lg"></i> Zaregistrovat se</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
index.php?account&login"><i class="fa fa-sign-in fa-lg"></i> Přihlásit se</a></li>
                            <?php } else { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_smarty_tpl->tpl_vars['userFullName']->value;?>
 <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
index.php?account&settings"><i class="fa fa-id-card fa-lg"></i> Nastavení</a></li>
                                    <li><a href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
index.php?account&logout"><i class="fa fa-sign-out fa-lg"></i> Odhlásit se</a></li>
                                </ul>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
        </nav>

        <main>       
            <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_190434849759e73e4a70aa17_34458570', 'content');
?>

        </main>

        <footer class="footer">
            <p class="p-to-center">© 2017 Jiří Valůšek</p>
        </footer>
    </body>
</html><?php }
/* {block 'content'} */
class Block_190434849759e73e4a70aa17_34458570 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <?php
}
}
/* {/block 'content'} */
}
