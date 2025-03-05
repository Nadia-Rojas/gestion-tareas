<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    /**
     * Verifica que la página de inicio se carga correctamente.
     *
     * @return void
     */
    public function test_home_page_loads_correctly()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
