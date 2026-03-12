<?php

namespace App\Service;

use App\Entity\User;

interface AiServiceInterface
{
    public function generateResponse(string $prompt, ?User $user = null): string;
}

