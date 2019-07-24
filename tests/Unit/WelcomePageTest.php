<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    /**
     * Test to see the welcome page text content.
     *
     * @return void
     */
    public function testPageContent()
    {
         $this->get('/')
            ->assertStatus(200)
            ->assertSeeText('Bank Security Guard Scheduler')
            ->assertSeeText('Click Here To Schedule');
    }
}
