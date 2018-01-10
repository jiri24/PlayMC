<?php
/* Smarty version 3.1.30, created on 2017-12-01 19:20:29
  from "/home/valuji00/views/templates/register.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5a219d6d7276e6_09438628',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3d379a500fa33a05bdaf2d91cf8d2237a4954fe3' => 
    array (
      0 => '/home/valuji00/views/templates/register.tpl',
      1 => 1512130797,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:index.tpl' => 1,
  ),
),false)) {
function content_5a219d6d7276e6_09438628 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_3829099105a219d6d7238a3_80605278', 'content');
$_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender("file:index.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, false);
}
/* {block 'content'} */
class Block_3829099105a219d6d7238a3_80605278 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <div class="container content">
        <h1 class='h1'>Registrace</h1>

        <?php if (count($_smarty_tpl->tpl_vars['messageDanger']->value) != 0) {?>
            <div class="alert alert-danger">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messageDanger']->value, 'message');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['message']->value) {
?>
                    <p><?php echo $_smarty_tpl->tpl_vars['message']->value;?>
</p>
                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

            </div>
        <?php }?>

        <form method="POST">
            <div class='row'>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_name">Jméno</label>
                        <input type="text" class="form-control" id="label_name" name="reg_name" value="<?php echo $_smarty_tpl->tpl_vars['regName']->value;?>
" placeholder="Jan" required>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_surname">Příjmení</label>
                        <input type="text" class="form-control" id="label_surname" name="reg_surname" value="<?php echo $_smarty_tpl->tpl_vars['regSurname']->value;?>
" placeholder="Novák" required>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-12'>
                    <div class="form-group">
                        <label for="label_email">Email</label>
                        <input type="email" class="form-control" id="label_email" name="reg_email" value="<?php echo $_smarty_tpl->tpl_vars['regEmail']->value;?>
" placeholder="jan.novak@example.com" required>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_password">Heslo</label>
                        <input type="password" class="form-control" id="label_password" name="reg_password" placeholder="******" required>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_password_confirm">Heslo znovu</label>
                        <input type="password" class="form-control" id="label_password_confirm" name="reg_password_confirm" placeholder="******" required>
                    </div>
                </div>
            </div>

            <div class="g-recaptcha" data-sitekey="6LchnyYUAAAAAOEzij_AdwdLu-XSU-WgknfDOwtW"></div>

            <!--<div class="checkbox">
                <label>
                    <input type="checkbox"> Souhlasím s podmínkami
                </label>
            </div>-->
            <button type="submit" class="btn btn-secondary"><i class="fa fa-pencil fa-lg"></i> Zaregistrovat se</button>
        </form>
    </div>
<?php
}
}
/* {/block 'content'} */
}
