<?php
/* Smarty version 3.1.30, created on 2017-11-04 11:36:41
  from "C:\xampp\htdocs\PlayMC\views\templates\home.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_59fd98398fde99_66382575',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5f4cf2762daa26a977741307c07f06cba6003394' => 
    array (
      0 => 'C:\\xampp\\htdocs\\PlayMC\\views\\templates\\home.tpl',
      1 => 1509791633,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:index.tpl' => 1,
  ),
),false)) {
function content_59fd98398fde99_66382575 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_166005848959fd98398fc880_86554394', 'content');
$_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender("file:index.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, false);
}
/* {block 'content'} */
class Block_166005848959fd98398fc880_86554394 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <div class="container content">
        <h1>Domů</h1>
        <div id="videoplayer" class="sizing mb-4">
            <div id="youtubeplayer"></div>
        </div>

        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
js/youtubeplayer.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
js/soundmanager2.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
js/audioplayer.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
js/player.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
>
            var player = new Player(false);
        <?php echo '</script'; ?>
>

        <?php
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['i']->step = 1;$_smarty_tpl->tpl_vars['i']->total = (int) ceil(($_smarty_tpl->tpl_vars['i']->step > 0 ? count($_smarty_tpl->tpl_vars['genres']->value)-1+1 - (0) : 0-(count($_smarty_tpl->tpl_vars['genres']->value)-1)+1)/abs($_smarty_tpl->tpl_vars['i']->step));
if ($_smarty_tpl->tpl_vars['i']->total > 0) {
for ($_smarty_tpl->tpl_vars['i']->value = 0, $_smarty_tpl->tpl_vars['i']->iteration = 1;$_smarty_tpl->tpl_vars['i']->iteration <= $_smarty_tpl->tpl_vars['i']->total;$_smarty_tpl->tpl_vars['i']->value += $_smarty_tpl->tpl_vars['i']->step, $_smarty_tpl->tpl_vars['i']->iteration++) {
$_smarty_tpl->tpl_vars['i']->first = $_smarty_tpl->tpl_vars['i']->iteration == 1;$_smarty_tpl->tpl_vars['i']->last = $_smarty_tpl->tpl_vars['i']->iteration == $_smarty_tpl->tpl_vars['i']->total;?>
            <?php if ($_smarty_tpl->tpl_vars['i']->value%4 == 0) {?>
                <div class="row mb-4">
                <?php }?>
                <div class="col-md-3">
                    <a href="#" class="genre-href" onclick="player.playGenre(<?php echo $_smarty_tpl->tpl_vars['genres']->value[$_smarty_tpl->tpl_vars['i']->value]['id'];?>
)">
                        <div class="genre genre<?php echo $_smarty_tpl->tpl_vars['genres']->value[$_smarty_tpl->tpl_vars['i']->value]['id'];?>
 p-2">
                            <?php echo $_smarty_tpl->tpl_vars['genres']->value[$_smarty_tpl->tpl_vars['i']->value]['name'];?>

                        </div>
                    </a>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['i']->value%4 == 3 || $_smarty_tpl->tpl_vars['i']->value == count($_smarty_tpl->tpl_vars['genres']->value)-1) {?>
                </div>
            <?php }?>
        <?php }
}
?>


        <div class="container fixed-bottom player">
            <div id="track-title"></div>
            <div class="btn-group" role="group" aria-label="Přehrávač">
                <div class="btn-group mr-2" role="group" aria-label="Přehrávání">
                    <button type="button" class="btn btn-dark" onclick="player.play()"><i id="play" class="fa fa-lg fa-play"></i></button>
                    <button type="button" class="btn btn-dark" onclick="player.next(player)"><i id="next" class="fa fa-lg fa-forward"></i></button>
                </div>
                <div class="btn-group mr-2" role="group" aria-label="Hodnocení">
                    <button type="button" class="btn btn-dark" onclick="like()"><i id="like" class="fa fa-lg fa-thumbs-up"></i></button>
                    <button type="button" class="btn btn-dark" onclick="dislike()"><i id="dislike" class="fa fa-lg fa-thumbs-down"></i></button>
                </div>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Režim">
                <button type="button" class="btn btn-dark" onclick="">Známé</button>
                <button type="button" class="btn btn-dark" onclick="">Automat</button>
                <button type="button" class="btn btn-dark" onclick="">Průzkum</button>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Typ přehrávače">
                <button type="button" class="btn btn-success" id="spotify" onclick="player.switchPlayer('spotify')"><i id="play" class="fa fa-lg fa-spotify"></i></button>
                <button type="button" class="btn btn-dark" id="youtube" onclick="player.switchPlayer('youtube')"><i id="next" class="fa fa-lg fa-youtube-play"></i></button>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Rok">
                <button type="button" class="btn btn-dark" onclick="player.playYear('')">Vše</button>
                <button type="button" class="btn btn-dark" onclick="player.playYear('10s')">Současnost</button>
                <button type="button" class="btn btn-dark" onclick="player.playYear('00s')">00s</button>
                <button type="button" class="btn btn-dark" onclick="player.playYear('90s')">90s</button>
                <button type="button" class="btn btn-dark" onclick="player.playYear('80s')">80s</button>
                <button type="button" class="btn btn-dark" onclick="player.playYear('70s')">70s</button>
                <button type="button" class="btn btn-dark" onclick="player.playYear('60s')">60s</button>
                <button type="button" class="btn btn-dark" onclick="player.playYear('50s')">50s</button>
            </div>
        </div>


        <!--
        <div class="btn-group" role="group" aria-label="Přehrávač">
            <div class="btn-group" role="group" aria-label="Přehrávání"></div>
            <div class="btn-group" role="group" aria-label="Další"></div>
        </div>-->
    </div>
</div>
<?php
}
}
/* {/block 'content'} */
}
