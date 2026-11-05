<?php

namespace Tests\Support;

use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * Safety guard: refresh migrations are allowed only on the dedicated MySQL
 * test database. Never run these tests against a working HEXAGON database.
 */
abstract class HexagonIsolatedTestCase extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        $connection = config('database.default');
        $database = config('database.connections.mysql.database');

        if ($connection !== 'mysql' || $database !== 'hexagon_domain_test') {
            throw new RuntimeException(
                'STOP: HEXAGON tests require DB_CONNECTION=mysql and '
                . 'DB_DATABASE=hexagon_domain_test. Actual connection: '
                . (string) $connection . '; database: ' . (string) $database
            );
        }
    }
}
