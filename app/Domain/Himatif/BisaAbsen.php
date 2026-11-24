<?php

namespace App\Domain\Himatif;

interface BisaAbsen
{
    public function alasanTidakBisaAbsen(Rapat $rapat, bool $sudahAbsen): ?string;
}
