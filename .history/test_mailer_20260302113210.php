<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

// Test email sending
echo "=== Test d'envoi d'email ===\n\n";

// Get DSN from mailer.yaml config
$dsn = 'smtp://constantkilayossi@gmail.com:dvmcagynrseciexs@smtp.gmail.com:587';

echo "Configuration SMTP:\n";
echo "DSN: " . preg_replace('/:([^@]+)@/', ':***@', $dsn) . "\n\n";

try {
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);
    
    $email = (new Email())
        ->from('constantkilayossi@gmail.com')
        ->to('constantkilayossi@gmail.com')
        ->subject('Test - Code de vérification KilysAgri')
        ->html('
            <h1>Test d\'envoi</h1>
            <p>Si vous recevez cet email, la configuration est correcte!</p>
            <div style="background-color: #f5f5f5; padding: 20px; text-align: center; font-size: 24px;">
                123456
            </div>
        ');
    
    $mailer->send($email);
    echo "✅ Email envoyé avec succès!\n";
    echo "Vérifiez votre boîte mail.\n";
} catch (\Exception $e) {
    echo "❌ Erreur lors de l'envoi:\n";
    echo $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), '535') !== false) {
        echo "\n⚠️ Erreur d'authentification!";
        echo "\nLe mot de passe d'application n'est pas valide.";
        echo "\nSuivez ces étapes:";
        echo "\n1. Allez sur https://myaccount.google.com/apppasswords";
        echo "\n2. Connectez-vous avec votre compte Google";
        echo "\n3. Générez un nouveau mot de passe d'application";
        echo "\n4. Mettez à jour votre fichier .env avec le nouveau mot de passe";
    }
}
