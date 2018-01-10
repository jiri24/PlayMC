{extends file='index.tpl'}
{block name=content}
    <h1 class='h1'>Nastavení</h1>

    {if count($messageDanger) neq 0}
        <div class="alert alert-danger">
            {foreach $messageDanger as $message}
                <p>{$message}</p>
            {/foreach}
        </div>
    {/if}

    {if count($messageSuccess) neq 0}
        <div class="alert alert-success">
            {foreach $messageSuccess as $message}
                <p>{$message}</p>
            {/foreach}
        </div>
    {/if}

    <form method="POST">
        <fieldset>
            <legend>Základní údaje</legend>
            <div class='row'>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_name">Jméno</label>
                        <input type="text" class="form-control" id="label_name" name="set_name" value="{$setName}" placeholder="Jan" required>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_surname">Příjmení</label>
                        <input type="text" class="form-control" id="label_surname" name="set_surname" value="{$setSurname}" placeholder="Novák" required>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-12'>
                    <div class="form-group">
                        <label for="label_email">Email</label>
                        <input type="email" class="form-control" id="label_email" name="set_email" value="{$setEmail}" placeholder="jan.novak@example.com" required>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Změna hesla</legend>
            <div class='row'>
                <div class='col-md-4'>
                    <div class="form-group">
                        <label for="label_password_old">Staré heslo</label>
                        <input type="password" class="form-control" id="label_password_old" name="set_password_old" placeholder="******">
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class="form-group">
                        <label for="label_password_new">Nové heslo</label>
                        <input type="password" class="form-control" id="label_password_new" name="set_password_new" placeholder="******">
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class="form-group">
                        <label for="label_password_confirm">Nové heslo znovu</label>
                        <input type="password" class="form-control" id="label_password_confirm" name="set_password_confirm" placeholder="******">
                    </div>
                </div>
            </div>
        </fieldset>
        <button type="submit" class="btn btn-default"><i class="fa fa-save fa-lg"></i> Uložit</button>
    </form>
{/block}