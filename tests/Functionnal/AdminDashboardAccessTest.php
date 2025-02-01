<?php

namespace App\Tests\Functionnal;

use App\Enum\RoleEnum;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\EntityManagerTrait;
use App\Tests\Helper\Trait\WebClientTrait;
use App\Tests\Helper\When;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminDashboardAccessTest extends WebTestCase
{
    use WebClientTrait;
    use EntityManagerTrait;

    protected function setUp(): void
    {
        $this->initClient();
        $this->initEntityManager();
        When::setClient($this->client);
    }

    public function test_anonym_user_is_redirect_when_trying_to_access_back_office(): void
    {
        When::access()
            ->withMethod('GET')
            ->withUri('/admin')
            ->build();

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/login');
    }

    public function test_simple_user_can_not_access_back_office(): void
    {
        $user = ThereIs::anUser()->build();
        $this->em->persist($user);
        $this->em->flush();

        $this->client->loginUser($user);

        When::access()
            ->withMethod('GET')
            ->withUri('/admin')
            ->build();

        $this->assertResponseStatusCodeSame(403);
    }

    public function test_admin_can_access_back_office(): void
    {
        $user = ThereIs::anUser()
            ->withAdditionnalRole(RoleEnum::ADMIN)
            ->build();
        $this->em->persist($user);
        $this->em->flush();

        $this->client->loginUser($user);

        When::access()
            ->withMethod('GET')
            ->withUri('/admin')
            ->build();

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects(sprintf('/admin/user/%s', $user->getId()));
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_banned_admin_can_not_access_back_office(): void
    {
        $user = ThereIs::anUser()
            ->withAdditionnalRole(RoleEnum::ADMIN)
            ->withAdditionnalRole(RoleEnum::BANNED)
            ->build();
        $this->em->persist($user);
        $this->em->flush();

        $this->client->loginUser($user);

        When::access()
            ->withMethod('GET')
            ->withUri('/admin')
            ->build();

        $this->assertResponseStatusCodeSame(403);
    }
}
