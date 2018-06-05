<?php require '../../bootstrap.php'; ?><!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Material + Vue.js | Hybrid Login</title>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script src="https://code.getmdl.io/1.3.0/material.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.green-light_green.min.css"/>
    <link href="./css/hybridlogin.css" rel="stylesheet">
</head>
<body>
<div class="mdl-grid" id="app">
    <div class="mdl-cell mdl-cell--4-col"></div>

    <div class="mdl-cell mdl-cell--4-col">

        <div class="demo-card-wide mdl-card mdl-shadow--2dp">

            <!-- HEADERS !-->
            <div class="mdl-card__title">
                <h2>
                    <div class="mdl-card__title-text">Hybrid Login</div>
                    <div v-show="isSignIn" class="mdl-card__subtitle-text">Sign in! Welcome back...</div>
                    <div v-show="isSignUp" class="mdl-card__subtitle-text">Sign up! Welcome!</div>
                    <div v-show="!isSignIn && !isSignUp" class="mdl-card__subtitle-text">Sign in, sign up, whatever...
                    </div>
                </h2>
            </div>

            <!-- LOGIN FORM CARD !-->
            <div v-show="!isLoggedIn">
                <div class="mdl-grid">
                    <form>
                        <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet">
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input v-model.lazy="email" v-on:change="checkEmail"
                                       class="mdl-textfield__input"
                                       type="email" id="email"/>
                                <label class="mdl-textfield__label" for="email">E-mail</label>
                            </div>

                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input v-model="password" v-on:keyup="checkPassword"
                                       class="mdl-textfield__input"
                                       type="password" id="password"/>
                                <label class="mdl-textfield__label" for="password">Password</label>
                            </div>
                            <div>
                                <button v-on:click="sign" type="button"
                                        class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
                                    Go
                                </button>
                            </div>
                        </div>
                        <ul id="v-for" class='mdl-list'>
                            <li v-for="(passwordMessage) in passwordMessages" class="mdl-list__item">
                                {{passwordMessage}}
                            </li>
                        </ul>
                        <div v-show="isProcessing">
                            <div id="p2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- LOGGED IN CARD !-->
            <div v-show="isLoggedIn">
                <div class="mdl-card__supporting-text">
                    <h5>Yayyy...</h5>
                    <h6>{{email}} is logged in.</h6>
                    <p>UUID: {{userUUID}}</p>
                </div>
                <div class="mdl-card__actions mdl-card--border">
                    <a v-on:click="logout" class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect">
                        Log out
                    </a>
                </div>
                <!--<div class="mdl-card__menu">
                    <button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">
                        <i class="material-icons">share</i>
                    </button>
                </div>!-->
            </div>
        </div>
    </div>

    <div class="mdl-cell mdl-cell--4-col"></div>
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

</body>
</html>
