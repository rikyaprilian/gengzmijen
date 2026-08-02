<?php

namespace App\Repositories\Contracts;

interface PortalRepositoryInterface
{
    /**
     * Mengambil seluruh data homepage portal.
     *
     * @return array
     */
    public function getHomepage(): array;
}