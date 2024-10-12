<?php
use App\Helpers\Env;

?>

<script src="https://login-sdk.xsolla.com/latest/"></script>

<fieldset>
    <!-- https://developers.xsolla.com/api/igs-bb/operation/create-order-with-item/ -->
    <legend>Create token (client side)</legend>
    <label>Project id <input id="project" type="text" value="<?= Env::get('XSOLLA_PROJECT_ID') ?>"></label><br><br>
    <label>Login id <input id="login" type="text" value="<?= Env::get('XSOLLA_LOGIN_ID') ?>"></label><br><br>
    <label>User token <input id="user" type="text" value=""></label> <button onclick="openWidget()">Auth</button><br><br>
    <label>Item sku <input id="sku" type="text" value="mysku01"></label><br><br>
    <label>JSON body <textarea cols="90" rows="40" id="body">
{
"sandbox": true
}
</textarea></label><br><br>
    <a href="https://developers.xsolla.com/api/igs-bb/operation/create-order-with-item/" target="_blank">Docs</a><br><br>
    <button onclick="getToken()">Get token</button><br><br>
    <div id="result">
    </div>
</fieldset>
<a href="/">server side</a>


<div id="xl_auth" style="width: 100%; height: 1000px"></div>

<script>
    // function for opening a widget by button
    let xl;
    function openWidget() {
        xl.open();
    }

    let token = new URLSearchParams(window.location.search).get('token');
    if (token) {
        document.getElementById('user').value = token;
    }

    function getToken() {
        let project = document.getElementById('project').value;
        let itemSku = document.getElementById('sku').value;
        let body = document.getElementById('body').value;
        let userToken = document.getElementById('user').value;

        let bodyJson;
        try {
            bodyJson = JSON.parse(body);
        } catch {
            alert('JSON in body is not valid');
            return;
        }

        let result = document.querySelector('#result');
        result.innerHTML = 'Loading...';

        fetch(
            `https://store.xsolla.com/api/v2/project/${project}/payment/item/${itemSku}`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: 'Bearer ' + userToken
                },
                body: body
            }
        )
        .then(resp => resp.json())
        .then(data => {
            console.log(data);

            let stringUrl = '';
            if (data.token) {
                stringUrl = `https://secure.xsolla.com/paystation4/?token=${data.token}`;
                if (bodyJson.sandbox === true) {
                    stringUrl = `https://sandbox-secure.xsolla.com/paystation4/?token=${data.token}`;
                }
            }

            result.innerHTML = JSON.stringify(data, null, 2) + `<br><br><a target="_blank" href="${stringUrl}">${stringUrl}</a>`;
        })
        .catch(err => console.error(err));
    }

    window.onload = function () {
        let loginId = document.getElementById('login').value;
        xl = new XsollaLogin.Widget({
            projectId: loginId,
            callbackUrl: "http://localhost:<?=  Env::get('BACKEND_PORT', '8080') ?>/client-side.php", // URL to redirect the user to after registration/authentication/password reset.
            preferredLocale: "en_XX"
        });
        xl.mount("xl_auth");
    };
</script>
