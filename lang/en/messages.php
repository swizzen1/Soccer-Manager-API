<?php

return [
    'auth' => [
        'register_success' => 'Registration completed successfully.',
        'login_success' => 'Logged in successfully.',
        'logout_success' => 'Logged out successfully.',
        'me_success' => 'Authenticated user retrieved successfully.',
        'invalid_credentials' => 'The provided credentials are invalid.',
        'unauthenticated' => 'Unauthenticated.',
        'forbidden' => 'This action is unauthorized.',
    ],
    'team' => [
        'show_success' => 'Team retrieved successfully.',
        'updated' => 'Team updated successfully.',
    ],
    'player' => [
        'index_success' => 'Players retrieved successfully.',
        'show_success' => 'Player retrieved successfully.',
        'updated' => 'Player updated successfully.',
    ],
    'transfer' => [
        'index_success' => 'Transfer market retrieved successfully.',
        'listed' => 'Player listed for transfer successfully.',
        'cancelled' => 'Transfer listing cancelled successfully.',
        'sold' => 'Player bought successfully.',
        'already_listed' => 'Player is already active on the transfer list.',
        'no_active_listing' => 'Player does not have an active transfer listing.',
        'not_active' => 'Transfer listing is not active.',
        'not_enough_budget' => 'Not enough budget.',
        'stale_listing' => 'Transfer listing is no longer valid for this player.',
    ],
    'errors' => [
        'not_found' => 'Resource not found.',
    ],
];
