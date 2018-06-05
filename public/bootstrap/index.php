<?php require '../../bootstrap.php'; ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bootstrap + Vue.js | Hybrid Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
          integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link href="./css/hybridlogin.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
</head>
<body>
<div id="app" class="text-center">
    <form class="form-signin">
        <h1 class="h3 mb-3 font-weight-normal">Hybrid Login</h1>

        <p v-show="isSignIn" class="mb-4 text-muted">Sign in! Welcome back...</p>
        <p v-show="isSignUp" class="mb-4 text-muted">Sign up! Welcome!</p>
        <p v-show="!isSignIn && !isSignUp" class="mb-4 text-muted">Sign in, sign up, whatever...</p>

        <!-- NOT LOGGED IN !-->
        <div v-show="!isLoggedIn">

            <label for="inputEmail" class="sr-only">Email address</label>
            <input v-model.lazy="email" v-on:change="checkEmail" type="email" id="inputEmail" class="form-control"
                   placeholder="Email address" required autofocus>

            <label for="inputPassword" class="sr-only">Password</label>
            <input v-model="password" v-on:keyup="checkPassword" type="password" id="inputPassword" class="form-control"
                   placeholder="Password" required>

            <button v-on:click="sign" class="btn btn-lg btn-primary btn-block" type="button">Go</button>
        </div>

        <!-- LOGGED IN !-->
        <div v-show="isLoggedIn">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Yayyy...</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{email}} is logged in.</h6>
                    <!--<p class="card-text">Yayyy</p>-->
                    <p class="card-text text-muted">UUID: {{userUUID}}</p>
                    <a v-on:click="logout" href="#" class="card-link">Please, log me out.</a>
                </div>
            </div>
        </div>

        <ul id="v-for" v-show="!isLoggedIn" class="list-group mt-4">
            <li v-for="(passwordMessage) in passwordMessages" class="list-group-item list-group-item-light">
                {{passwordMessage}}
            </li>
        </ul>

        <div v-if="isProcessing">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                     aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
            </div>
            <!--p class="mb-4 text-muted">Loading...</p!-->
        </div>
    </form>
</div>

<script type="text/javascript">
    var vm = new Vue({
        el: '#app',
        data: {
            // State control
            isProcessing: false,
            isSignIn: false,
            isSignUp: false,
            isLoggedIn: <?=!empty($_SESSION['loggedUserUUID']) ? 'true' : 'false'?>,
            isSignedUp: <?=!empty($_SESSION['loggedUserUUID']) ? 'true' : 'false'?>,
            // User data
            userUUID: '<?=$_SESSION['loggedUserUUID'] ?? ''?>',
            email: '<?=$_SESSION['loggedUserEmail'] ?? ''?>',
            password: '',
            // Password & validation
            passwordMessages: [],
            passwordRestrictions: <?=json_encode(\HybridLogin\User\UserPassword::getRestrictions());?>,
        },
        methods: {
            checkEmail: function () {
                if (vm.email.length === 0) {
                    vm.isSignUp = false;
                    vm.isSignIn = false;
                } else {
                    vm.isProcessing = true;
                    fetch('/user/isRegistered?email=' + encodeURIComponent(vm.email)
                        + '&sessionId=' + '<?=session_id();?>'
                    )
                        .then(queryResponse => queryResponse.json())
                        .then(queryResponse => {
                            vm.isProcessing = false;
                            vm.isSignUp = true;
                            vm.isSignIn = false;
                            if (queryResponse.success && queryResponse.data.isRegistered) {
                                vm.isSignIn = true;
                                vm.isSignUp = false;
                            }
                        });
                }

            },
            checkPassword: function () {
                vm.passwordMessages = [];
                vm.passwordRestrictions.forEach(function (passwordRestriction) {
                    var passwordRestrictionRegex = eval(passwordRestriction.regularExpression);
                    if (!passwordRestrictionRegex.test(vm.password)) {
                        vm.passwordMessages.push(passwordRestriction.message);
                    }
                })
            },
            sign: function () {
                vm.checkPassword();
                if (vm.passwordMessages.length === 0 && vm.isSignIn) {
                    vm.isProcessing = true;
                    fetch('/user/signIn', {
                        method: 'POST',
                        headers: {
                            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
                        },
                        body: 'email=' + encodeURIComponent(vm.email)
                        + '&password=' + encodeURIComponent(vm.password)
                        + '&sessionId=' + '<?=session_id();?>'
                    })
                        .then(queryResponse => queryResponse.json())
                        .then(queryResponse => {
                            console.log(queryResponse);
                            vm.isProcessing = false;
                            vm.isLoggedIn = false;

                            if (queryResponse.success && queryResponse.data.isLoggedIn) {
                                vm.isLoggedIn = true;
                                vm.userUUID = queryResponse.data.userUUID.toString();
                            }

                            if (queryResponse.errors) {
                                if (queryResponse.errors) {
                                    vm.passwordMessages = queryResponse.errors;
                                }
                            }
                        });
                } else if (vm.passwordMessages.length === 0 && vm.isSignUp) {
                    vm.isProcessing = true;
                    fetch('/user/signUp', {
                        method: 'POST',
                        headers: {
                            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
                        },
                        body: 'email=' + encodeURIComponent(vm.email)
                        + '&password=' + encodeURIComponent(vm.password)
                        + '&sessionId=' + '<?=session_id();?>'
                    })
                        .then(queryResponse => queryResponse.json())
                        .then(queryResponse => {
                            vm.isProcessing = false;
                            vm.isProcessing = false;
                            vm.isLoggedIn = false;

                            if (queryResponse.success && queryResponse.data.isLoggedIn) {
                                vm.isLoggedIn = true;
                                vm.userUUID = queryResponse.data.userUUID.toString();
                            }

                            if (queryResponse.errors) {
                                if (queryResponse.errors) {
                                    vm.passwordMessages = queryResponse.errors;
                                }
                            }
                        });
                }
            },
            logout: function () {
                vm.isProcessing = true;
                fetch('/user/logout'
                    + '?sessionId=' + '<?=session_id();?>'
                )
                    .then(queryResponse => queryResponse.json())
                    .then(queryResponse => {
                        vm.isProcessing = false;
                        vm.isSignUp = false;
                        vm.isSignIn = true;
                        vm.userUUID = null;
                        vm.password = null;
                        if (queryResponse.success) {
                            vm.isLoggedIn = false;
                            vm.isSignedUp = false;
                        }
                    });

            }
        }
    });

    // On page ready
    (function () {
        vm.checkPassword();
    })();
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
        crossorigin="anonymous"></script>
</body>
</html>
