<?php

namespace App\Services;


class ClassService {
    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(25)), '+/', '-_'), '=');
    }
}
