<?php

namespace BitolaCo\MemberClicks;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

class BaseTest extends TestCase
{
    protected $memberclicks;

    protected function setUp()
    {
        // Check this directory and all parent folders up to the project
        // base directory for a .env file, and load it if it exists.
        $dotenvDir = __DIR__;
        for ($i = 0; $i < 4; $i++) {
            if (file_exists($dotenvDir.'/.env')) {
                $dotenv = new Dotenv($dotenvDir);
                $dotenv->load();
                break;
            }
            $dotenvDir = dirname($dotenvDir);
        }

        $this->memberclicks = new MemberClicks(getenv('MEMBERCLICKS_ORG_ID'), getenv('MEMBERCLICKS_CLIENT_ID'), getenv('MEMBERCLICKS_CLIENT_SECRET'));
        $this->memberclicks->auth();
    }

    function testInit() {
        $this->assertFalse(empty($this->memberclicks));
    }
}
