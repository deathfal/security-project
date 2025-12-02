<?php

namespace App\Tests\Unit;

use App\Repository\WalletRepository;
use App\Twig\WalletExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class WalletExtensionTest extends TestCase
{
    private WalletExtension $walletExtension;
    private Security $security;
    private WalletRepository $walletRepository;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->walletExtension = new WalletExtension($this->security, $this->walletRepository);
    }

    public function testFormatBalance(): void
    {
        $result = $this->walletExtension->formatBalance("123.456", "USD");
        $this->assertEquals("123.46", $result);
    }
}
