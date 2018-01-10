{extends file='index.tpl'}
{block name=content}
    <div class="container content">
        <h1 class="h1">Přihlásit se</h1>
        {if count($messageDanger) neq 0}
            <div class="alert alert-danger">
                {foreach $messageDanger as $message}
                    <p>{$message}</p>
                {/foreach}
            </div>
        {/if}

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
{/block}