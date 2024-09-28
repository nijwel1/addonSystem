<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase {
    /**
     * A basic feature test example.
     */
    public function it_can_get_full_name(): void {

        // Arrange
        $user = new User( [
            'first_name' => 'John',
            'last_name'  => 'Doe',
        ] );

        // Act
        $fullName = $user->fullName();

        // Assert
        $this->assertEquals( 'John Doe', $fullName );

    }
}
