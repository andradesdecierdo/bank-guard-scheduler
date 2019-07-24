<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Guard;

class GuardPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Send add guard request.
     *
     * @param $name
     * @param $colorIndicator
     *
     * @return \Illuminate\Foundation\Testing\TestResponse $response
     */
    private function sendAddGuardRequest($name, $colorIndicator)
    {
        $response = $this->call( 'POST','/guard', [
            'name' => $name,
            'color_indicator' => $colorIndicator,
        ]);
        while ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        return $response;
    }

    /**
     * Send delete guard request.
     *
     * @param $guardId
     *
     * @return \Illuminate\Foundation\Testing\TestResponse $response
     */
    private function sendDeleteGuardRequest($guardId)
    {
        $response = $this->call( 'DELETE','/guard', ['guard_id' => $guardId]);
        while ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        return $response;
    }

    /**
     * Test to see the guard page text content.
     *
     * @return void
     */
    public function testPageContent()
    {
        $this->get('/guard')
            ->assertStatus(200)
            //header
            ->assertSeeText('Bank Guard Scheduler')
            ->assertSeeText('Schedules')
            ->assertSeeText('Guards')
            //add security guard form
            ->assertSeeText('Add Security Guard')
            ->assertSeeText('Name:')
            ->assertSeeText('Color:')
            ->assertSeeText('Submit')
            //delete security guard form
            ->assertSeeText('Delete Security Guard')
            ->assertSeeText('Guard:')
            //bank security guards table
            ->assertSeeText('List of Security Guards')
            ->assertSeeText('Name')
            ->assertSeeText('Color Indicator');
    }

    /**
     * Test for adding a valid guard and color indicator.
     *
     * @return void
     */
    public function testAddGuardSuccessful()
    {
        $this->get('/guard')
            ->assertStatus(200);
        $response = $this->sendAddGuardRequest(
            'Billy Smith',
            '#ffc107'
        );
        $response->assertStatus(200)
            ->assertSeeText('Billy Smith')
            //success message
            ->assertSeeText('Guard successfully added.');
    }

    /**
     * Test for deleting a valid guard.
     *
     * @return void
     */
    public function testDeleteGuardSuccessful()
    {
        $guard = factory(Guard::class)->create();

        $this->get('/guard')
            ->assertStatus(200);
        $response = $this->sendDeleteGuardRequest($guard->id);
        $response->assertStatus(200)
            ->assertDontSeeText($guard->name)
            //success message
            ->assertSeeText('Guard successfully deleted.');
    }

    /**
     * Test the validation for adding an existing guard and color indicator.
     *
     * @return void
     */
    public function testAddExistingGuardAndColorIndicator()
    {
        $guard = factory(Guard::class)->create();

        $this->get('/guard')
            ->assertStatus(200);
        $response = $this->sendAddGuardRequest(
            $guard->name,
            $guard->color_indicator
        );
        $response->assertStatus(200)
            //error messages
            ->assertSeeText('The name has already been taken.')
            ->assertSeeText('The color indicator has already been taken.');
    }

    /**
     * Test the redirection to individual guard schedule page.
     *
     * @return void
     */
    public function testRedirectToIndividualGuardSchedulePage()
    {
        $guard = factory(Guard::class)->create();

        $this->get('/guard')
            ->assertStatus(200);
        $this->get('/schedule/'.$guard->id)
            ->assertStatus(200)
            ->assertSeeText('Security Guard Schedules')
            ->assertSeeText($guard->name);
    }
}
