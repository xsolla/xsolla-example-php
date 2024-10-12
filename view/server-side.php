<?php
use App\Helpers\Env;
?>

<fieldset>
    <legend>Create token (server side)</legend>
    <label>Project id <input id="project" type="text" value="<?= Env::get('XSOLLA_PROJECT_ID') ?>"></label><br><br>
    <label>Api key <input id="api-key" type="text" value="<?= Env::get('XSOLLA_API_KEY') ?>"></label><br><br>
    <label>JSON body <br><textarea cols="90" rows="40" id="body">
{
  "sandbox": true,
  "user": {
    "id": {
      "value": "user-id"
    },
    "country": {
      "value": "US"
    }
  },
  "purchase": {
    "items": [
      {
        "sku": "mysku01",
        "quantity": 1
      }
    ]
  }
}
</textarea></label><br><br>
    <a href="https://developers.xsolla.com/api/igs-bb/operation/admin-create-payment-token/" target="_blank">Docs</a><br><br>
    <button onclick="getToken()">Get token</button><br><br>
    <div id="result"></div>
</fieldset>

<a href="/client-side.php">client side</a>

<script>
    function getToken() {

        // all this part can be hidden on server. Just for comfortable example we will use it here
        let project = document.getElementById('project').value;
        let apiKey = document.getElementById('api-key').value;
        let bodyText = document.getElementById('body').value;

        let bodyObj = null;
        try {
            bodyObj = JSON.parse(bodyText);
        } catch (e) {
            alert('JSON in body is not valid');
        }

        let result = document.querySelector('#result');
        result.innerHTML = 'Loading...';

        fetch(
            `http://localhost:<?= Env::get('BACKEND_PORT', '8080') ?>/server-side-token-generation.php`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    project: project,
                    apikey: apiKey,
                    body: bodyText
                })
            }
        )
        .then(resp => resp.json())
        .then(data => {
            let stringUrl = '';
            if (data.token) {
                stringUrl = `https://secure.xsolla.com/paystation4/?token=${data.token}`;
                if (bodyObj.sandbox === true) {
                    stringUrl = `https://sandbox-secure.xsolla.com/paystation4/?token=${data.token}`;
                }
            }

            result.innerHTML = JSON.stringify(data, null, 2) + `<br><br><a target="_blank" href="${stringUrl}">${stringUrl}</a>`;
        })
        .catch(err => {
            console.error(err);
            result.innerHTML = 'Error: ' + err;
        });
    }
</script>
