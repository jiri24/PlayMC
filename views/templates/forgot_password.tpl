{extends file='index.tpl'}
{block name=content}
    <div class="container content">
        <h1 class='h1'>Zapomenuté heslo</h1>

        {if count($messageDanger) neq 0}
            <div class="alert alert-danger pb-0">
                {foreach $messageDanger as $message}
                    <p>{$message}</p>
                {/foreach}
            </div>
        {/if}

        {if count($messageSuccess) neq 0}
            <div class="alert alert-success pb-0">
                {foreach $messageSuccess as $message}
                    <p>{$message}</p>
                {/foreach}
            </div>
        {/if}

        <form method="POST">
            <div class="form-group">
                <label for="label_email">Email</label>
                <input type="email" class="form-control" id="label_email" name="email" placeholder="jan.novak@example.com" required>
            </div>
            <button type="submit" class="btn btn-secondary"><i class="fa fa-send fa-lg"></i> Odeslat</button>
        </form>
    </div>
{/block}