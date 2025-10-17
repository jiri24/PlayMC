{extends file='index.tpl'}
{block name=content}
    <div class="container content">
        <h1 class='h1'>Obnovení hesla</h1>

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
            <div class='row'>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_password_new">Nové heslo</label>
                        <input type="password" class="form-control" id="label_password_new" name="set_password_new" placeholder="******">
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_password_confirm">Nové heslo znovu</label>
                        <input type="password" class="form-control" id="label_password_confirm" name="set_password_confirm" placeholder="******">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-secondary"><i class="fa fa-save fa-lg"></i> Uložit</button>
        </form>
    </div>
{/block}