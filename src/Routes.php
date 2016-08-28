<?php

namespace Example;

return [
    ['GET', '/', ['Example\Controllers\IndexController', 'index']],

    ['GET', '/login', ['Example\Controllers\LoginController', 'index']],
    ['POST', '/login', ['Example\Controllers\LoginController', 'login']],

    ['GET', '/logout', ['Example\Controllers\LoginController', 'logout']],

    ['GET', '/registration', ['Example\Controllers\RegistrationController', 'index']],
    ['POST', '/registration', ['Example\Controllers\RegistrationController', 'register']],
    ['GET', '/registration/verify/{userId}/{verCode}', ['Example\Controllers\RegistrationController', 'verify']]
];
