<?php
session_start();
require '../../vendor/autoload.php';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Material + Vue.js | Hybrid Login</title>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.green-light_green.min.css"/>
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@3.5.1" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class="mdl-grid" id="app">
    <div class="mdl-cell mdl-cell--4-col"></div>
    <div class="mdl-cell mdl-cell--4-col">
        <header class="mdl-layout__header">
            <div class="mdl-layout-icon"></div>
            <div class="mdl-layout__header-row">
                <h1 class="mdl-layout__title">Hybrid Login</h1>
            </div>
            <div class="mdl-layout__header-row">
                <!--<i class="material-icons">account_circle</i> {{email}}
                <a href="."><i class="material-icons">highlight_off</i> Logout</a>-->
                <p v-show="isLoggedIn"><i class="material-icons">account_circle</i> {{email}} <a href="../">Logout</a>
                </p>
            </div>
        </header>
        <div class="mdl-grid">
            <div class="mdl-cell mdl-cell--12-col mdl-cell--8-col-tablet">
                <div style="height: 100px; overflow: hidden;">
                    <transition name="custom-classes-transition" ss
                                enter-active-class="animated rubberBand"
                                leave-active-class="animated bounceOutUp">
                        <h4 v-show="isSignIn">Sign in! Welcome back...</h4>
                    </transition>
                    <transition name="custom-classes-transition"
                                enter-active-class="animated rotateInDownLeft"
                                leave-active-class="animated bounceOutUp">
                        <h4 v-show="isSignUp">Sign up! Welcome!</h4>
                    </transition>
                    <transition name="custom-classes-transition"
                                enter-active-class="animated bounceInDown"
                                leave-active-class="animated bounceOutUp">
                        <h4 v-show="!isSignIn && !isSignUp">Sign in, sign up, whatever...</h4>
                    </transition>

                </div>

                <transition name="custom-classes-transition"
                            enter-active-class="animated wobble"
                            leave-active-class="animated bounceOutRight">
                    <div v-show="isSignedUp">
                        <h4>Success! You're signed up.</h4>
                    </div>
                </transition>


                <transition name="custom-classes-transition"
                            enter-active-class="animated wobble"
                            leave-active-class="animated bounceOutRight">
                    <div v-show="isLoggedIn">
                        <h4>{{email}} is logged in.</h4>
                    </div>
                </transition>

                <transition name="fade" mode="out-in">
                    <form v-show="!isLoggedIn" action="#">
                        <div>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input v-model.lazy="email" v-on:change="checkEmail" class="mdl-textfield__input"
                                       type="email" id="email"/>
                                <label class="mdl-textfield__label" for="email">E-mail</label>
                                <!--                        <i class="material-icons">check_circle</i>
                                -->                    </div>
                        </div>

                        <div>
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input v-model="password" v-on:keyup="checkPassword" class="mdl-textfield__input"
                                       type="password" id="password"/>
                                <label class="mdl-textfield__label" for="password">Password</label>
                                <!--                        <i class="material-icons">check_circle</i>
                                -->                    </div>
                            <ul id="v-for">
                                <li v-for="(passwordMessage) in passwordMessages">
                                    {{passwordMessage}}
                                </li>
                            </ul>
                        </div>

                        <div>
                            <button v-on:click="sign" type="button"
                                    class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
                                Ok
                            </button>
                        </div>
                        <div v-if="isProcessing">
                            <div id="loading"
                                 class="mdl-spinner mdl-spinner--single-color mdl-js-spinner is-active"></div>
                        </div>
                    </form>
                </transition>
            </div>
        </div>
        <div class="mdl-cell mdl-cell--4-col"></div>
    </div>
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
                    fetch('/?route=user&action=isRegistered&email=' + encodeURIComponent(vm.email)
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
                    fetch('/', {
                        method: 'POST',
                        headers: {
                            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
                        },
                        body: 'route=user&action=signIn'
                        + '&email=' + encodeURIComponent(vm.email)
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
                    fetch('/', {
                        method: 'POST',
                        headers: {
                            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
                        },
                        body: 'route=user&action=signUp'
                        + '&email=' + encodeURIComponent(vm.email)
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
                fetch('/?route=user&action=logout'
                    + '&sessionId=' + '<?=session_id();?>'
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
