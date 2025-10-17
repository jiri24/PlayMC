{extends file='index.tpl'}
{block name=content}
    <div class="container content">
        <h1 class='h1'>Registrace</h1>

        {if count($messageDanger) neq 0}
            <div class="alert alert-danger pb-0">
                {foreach $messageDanger as $message}
                    <p>{$message}</p>
                {/foreach}
            </div>
        {/if}

        <form method="POST">
            <div class='row'>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_name">Jméno</label>
                        <input type="text" class="form-control" id="label_name" name="reg_name" value="{$regName}" placeholder="Jan" required>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_surname">Příjmení</label>
                        <input type="text" class="form-control" id="label_surname" name="reg_surname" value="{$regSurname}" placeholder="Novák" required>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-12'>
                    <div class="form-group">
                        <label for="label_email">Email</label>
                        <input type="email" class="form-control" id="label_email" name="reg_email" value="{$regEmail}" placeholder="jan.novak@example.com" required>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_password">Heslo</label>
                        <input type="password" class="form-control" id="label_password" name="reg_password" placeholder="******" required>
                        <small id="emailHelp" class="form-text text-muted">Heslo musí být dlouhé minimálně 6 znaků.</small>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class="form-group">
                        <label for="label_password_confirm">Heslo znovu</label>
                        <input type="password" class="form-control" id="label_password_confirm" name="reg_password_confirm" placeholder="******" required>
                    </div>
                </div>
            </div>

            <div class="g-recaptcha" data-sitekey="6LchnyYUAAAAAOEzij_AdwdLu-XSU-WgknfDOwtW"></div>

            <!--<div class="checkbox">
                <label>
                    <input type="checkbox"> Souhlasím s podmínkami
                </label>
            </div>-->
            <button type="submit" class="btn btn-secondary mt-2"><i class="fa fa-pencil fa-lg"></i> Zaregistrovat se</button>
        </form>
    </div>
{/block}