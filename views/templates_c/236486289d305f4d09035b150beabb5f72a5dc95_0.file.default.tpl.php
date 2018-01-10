<?php
/* Smarty version 3.1.30, created on 2017-10-18 13:36:43
  from "C:\xampp\htdocs\PlayMC\views\templates\default.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_59e73ccb25ad61_52202566',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '236486289d305f4d09035b150beabb5f72a5dc95' => 
    array (
      0 => 'C:\\xampp\\htdocs\\PlayMC\\views\\templates\\default.tpl',
      1 => 1508326602,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:index.tpl' => 1,
  ),
),false)) {
function content_59e73ccb25ad61_52202566 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_63702854959e73ccb25a254_84969502', 'content');
$_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender("file:index.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, false);
}
/* {block 'content'} */
class Block_63702854959e73ccb25a254_84969502 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <div class="container">
        <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-100" src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
/images/1.png" alt="">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
/images/2.jpg" alt="">
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
/images/3.jpg" alt="">
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="jumbotron">
            <h1 class="display-3">Úvod</h1>
            <p class="lead">PlayMC je aplikace pro doporučování hudby. Zjistěte, co vám doporučí, stačí se zaregistrovat.</p>
            <p class="lead">
                <a class="btn btn-secondary btn-lg" href="<?php echo $_smarty_tpl->tpl_vars['webPath']->value;?>
index.php?registration&register" role="button"><i class="fa fa-pencil fa-lg"></i> Zaregistrovat se</a>
            </p>
        </div>
    </div>
<?php
}
}
/* {/block 'content'} */
}
