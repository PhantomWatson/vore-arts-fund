<?php
declare(strict_types=1);

namespace App\Test\TestCase;

use Cake\ORM\TableRegistry;

/**
 * Logs in as a user in integration tests
 *
 * The Authentication plugin's session authenticator expects the user entity to be stored under the `Auth` key.
 */
trait LoginTrait
{
    /**
     * Sets the session so that subsequent requests are authenticated as the specified user
     *
     * @param int $userId ID of a user in UsersFixture
     * @return void
     */
    protected function loginAs(int $userId): void
    {
        $user = TableRegistry::getTableLocator()->get('Users')->get($userId);
        $this->session(['Auth' => $user]);
    }
}
