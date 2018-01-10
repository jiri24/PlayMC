<?php
/* Smarty version 3.1.30, created on 2017-10-18 13:46:56
  from "C:\xampp\htdocs\PlayMC\views\templates\login.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_59e73f30316eb0_16379486',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '693f1288b7e9db45b6053bd4d3487d58a0330c7d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\PlayMC\\views\\templates\\login.tpl',
      1 => 1508327211,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:index.tpl' => 1,
  ),
),false)) {
function content_59e73f30316eb0_16379486 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_23009398259e73f303166b2_97429021', 'content');
$_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender("file:index.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, false);
}
/* {block 'content'} */
class Block_23009398259e73f303166b2_97429021 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <div class="container content">
        <h1 class="h1">Přihlásit se</h1>
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
            <div class="form-group">
                <label for="label_email">E-Mail</label>
                <input type="text" class="form-control" id="label_email" name="auth_email" placeholder="jan.novak@example.com" required>
            </div>
            <div class="form-group">
                <label for="label_password">Heslo</label>
                <input type="password" class="form-control" id="label_password" name="auth_password" placeholder="******" required>
            </div>
            <button type="submit" class="btn btn-secondary"><i class="fa fa-sign-in fa-lg"></i> Přihlásit se</button>
        </form>
    </div>
<?php
}
}
/* {/block 'content'} */
}
