{extends file='index.tpl'}
{block name=content}
    <h1 class="h1">{$messageHeader}</h1>
    <div class="alert alert-success">
        {foreach $messageSuccess as $message}
            <p>{$message}</p>
        {/foreach}
    </div>
{/block}