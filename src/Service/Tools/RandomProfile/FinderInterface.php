<?php

namespace App\Service\Tools\RandomProfile;


interface FinderInterface
{
    /**
     * @param array|null $params
     * @return mixed
     */
    public function find(array $params = null);
}
