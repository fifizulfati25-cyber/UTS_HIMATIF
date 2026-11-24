<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_page_displays_public_himatif_information(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('HIMATIF — Himpunan Mahasiswa Teknik Informatika')->assertSee('Program Kerja')->assertDontSee('Peran dalam Aplikasi');
    }
}
