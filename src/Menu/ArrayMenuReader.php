<?php

namespace Example\Menu;

class ArrayMenuReader implements MenuReader
{
    public function readMenu()
    {
        return [
            ['href' => '/', 'text' => 'Homepage'],
            ['href' => '/login', 'text' => 'Login'],
            ['href' => '/logout', 'text' => 'Logout '],
            ['href' => '/registration', 'text' => 'Registration'],
        ];
    }
}
