<?php
require_once __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Translate\V3\TranslationServiceClient;

// Đặt đường dẫn đến file JSON credentials
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/translate-key.json');

// Thay bằng Project ID thật từ Google Cloud Console
$projectId = 'php-translate-467409';
$location = 'global';

$client = new TranslationServiceClient();
$parent = $client->locationName($projectId, $location);

$response = $client->translateText([
    'contents' => ['こんにちは'],
    'targetLanguageCode' => 'en',
    'mimeType' => 'text/plain',
    'parent' => $parent,
]);

foreach ($response->getTranslations() as $translation) {
    echo 'Translated text: ' . $translation->getTranslatedText() . PHP_EOL;
    echo 'Detected source language: ' . $translation->getDetectedLanguageCode() . PHP_EOL;
}
