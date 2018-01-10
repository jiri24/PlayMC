{extends file='index.tpl'}
{block name=content}
    <h1 class="h1">Došlo k chybě</h1>
    <div class="alert alert-danger">
        {foreach $messageDanger as $message}
            <p>{$message}</p>
        {/foreach}
    </div>
{/block}