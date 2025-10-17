{extends file='index.tpl'}
{block name=content}
    <div class="container content">
        <h1 class="h1">{$messageHeader}</h1>
        <div class="alert alert-success pb-0">
            {foreach $messageSuccess as $message}
                <p>{$message}</p>
            {/foreach}
        </div>
    </div>
{/block}