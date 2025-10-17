{extends file='index.tpl'}
{block name=content}
    <div class="container content">
        <h1 class="h1">Došlo k chybě</h1>
        <div class="alert alert-danger pb-0">
            {foreach $messageDanger as $message}
                <p>{$message}</p>
            {/foreach}
        </div>
    </div>
{/block}