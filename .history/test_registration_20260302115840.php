<?php

require __DIR__.'/vendor/autoload.php';

use App\Entity\User;
use App\Service\OtpService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;

$kernel = require __DIR__.'/config/bootstrap.php';
$kernel->boot();

$container = $kernel->getContainer();
$otpService = $container->get(OtpService::class);
$entityManager = $container->get(EntityManagerInterface::class);

// Create a test user
$user = new User();
$user->setEmail('test-' . time() . '@example.com');
$user->setPassword('hashed_password');
$user->setIsVerified(false);

$entityManager->persist($user);
$entityManager->flush();

echo "Test user created: " . $user->getEmail() . "\n";
echo "User ID: " . $user->getId() . "\n";

// Generate and send OTP
echo "Generating OTP...\n";
$code = $otpService->generateAndSendOtp($user);

echo "OTP code generated: " . $code . "\n";
echo "Check the email inbox for confirmation code!\n";
