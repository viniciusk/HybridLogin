<?php require '../../bootstrap.php'; ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vuetifyjs | HybridLogin</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet"
          type="text/css">
    <link href="https://unpkg.com/vuetify/dist/vuetify.min.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="app">
    <v-app>
        <!--<v-toolbar
                app
                :clipped-left="clipped"
        >
            <v-toolbar-title v-text="title"></v-toolbar-title>
            <v-spacer></v-spacer>
        </v-toolbar>-->

        <v-content>

            <!--<SignForm/>-->
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <!-- FORM !-->
                    <v-flex xs12 sm8 md4>
                        <v-card class="elevation-12">
                            <v-toolbar dark color="primary">
                                <v-toolbar-title v-text="title"></v-toolbar-title>
                                <v-spacer></v-spacer>
                                <!-- HEADERS !-->
                                <div>
                                    <div v-show="isSignIn" class="mdl-card__subtitle-text">Sign in! Welcome back...</div>
                                    <div v-show="isSignUp" class="mdl-card__subtitle-text">Sign up! Welcome!</div>
                                    <div v-show="!isSignIn && !isSignUp" class="mdl-card__subtitle-text">Sign in, sign up, whatever...
                                    </div>
                                </div>
                            </v-toolbar>

                            <div  v-show="!isLoggedIn">
                                <v-card-text>
                                    <v-form>
                                        <v-text-field v-model.lazy="email" v-on:change="checkEmail" prepend-icon="person"
                                                      name="email" id="email"
                                                      label="E-mail" type="email"></v-text-field>
                                        <v-text-field id="password" name="password" label="Password"
                                                      prepend-icon="lock"
                                                      v-model="password" v-on:keyup="checkPassword"
                                                      type="password"></v-text-field>
                                    </v-form>

                                    <ul id="v-for" class='mdl-list'>
                                        <li v-for="(passwordMessage) in passwordMessages" class="mdl-list__item">
                                            {{passwordMessage}}
                                        </li>
                                    </ul>
                                    <div v-show="isProcessing">
                                        <div id="p2" class="mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>
                                    </div>

                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="primary" v-on:click="sign">Login</v-btn>
                                </v-card-actions>
                            </div>

                            <div  v-show="isLoggedIn">
                                <v-card-text>
                                    <div class="mdl-card__supporting-text">
                                        <h2>Yayyy...</h2>
                                        <h4>{{email}} is logged in.</h4>
                                        <p>UUID: {{userUUID}}</p>
                                    </div>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="primary" v-on:click="logout">Logout</v-btn>
                                </v-card-actions>
                            </div>

                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>

            <!--<v-content>
                <Test/>
                &lt;!&ndash;HelloWorld/&ndash;&gt;
            </v-content>-->

        </v-content>
        <!--<v-footer :fixed="fixed" app>
            <span>by Vinicius Kienen</span>
        </v-footer>-->
    </v-app>
</div>

<script src="https://unpkg.com/vue/dist/vue.js"></script>
<script src="https://unpkg.com/vuetify/dist/vuetify.js"></script>
<script>
    // import SignForm from 'components/SignForm';

    var vm = new Vue({
        el: '#app',
        /*components: {
            SignForm,
        },*/
        data: {
            clipped: true,
            drawer: true,
            fixed: false,
            miniVariant: true,
            right: true,
            rightDrawer: false,
            title: 'HybridLogin',
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
                });
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
