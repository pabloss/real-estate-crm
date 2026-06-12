<?php

declare(strict_types=1);

namespace App\Modules\PropertyCatalog\Infrastructure\Service;

use RuntimeException;

final readonly class ImageProcessor
{
    public function __construct(
        private string $projectDir
    ) {
    }

    public function processAndWatermark(string $sourcePath, string $targetFilename): string
    {
        $info = getimagesize($sourcePath);
        if ($info === false) {
            throw new RuntimeException('Nieprawidłowy plik obrazu.');
        }

        [$width, $height, $type] = $info;

        // Wczytanie obrazu
        $image = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            default => throw new RuntimeException('Nieobsługiwany format obrazu.'),
        };

        // Skalowanie (max szerokość 1200px)
        $newWidth = 1200;
        $newHeight = (int) ($height * ($newWidth / $width));

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Dodanie znaku wodnego (prosty tekst w prawym dolnym rogu)
        $textColor = imagecolorallocate($resizedImage, 255, 255, 255);
        $shadowColor = imagecolorallocate($resizedImage, 0, 0, 0);
        $watermarkText = 'CRM - ' . date('Y');

        // Cień
        imagestring($resizedImage, 5, $newWidth - 100, $newHeight - 30, $watermarkText, $shadowColor);
        // Główny tekst
        imagestring($resizedImage, 5, $newWidth - 101, $newHeight - 31, $watermarkText, $textColor);

        // Docelowy katalog publiczny
        $targetDir = $this->projectDir . '/public/uploads/properties';
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
            throw new RuntimeException('Nie można utworzyć katalogu docelowego.');
        }

        $targetPath = $targetDir . '/' . $targetFilename;
        imagejpeg($resizedImage, $targetPath, 85); // Zapis z kompresją 85%

        // Czyszczenie pamięci i pliku tymczasowego
        imagedestroy($image);
        imagedestroy($resizedImage);
        unlink($sourcePath);

        // Zwracamy relatywną ścieżkę do bazy danych
        return '/uploads/properties/' . $targetFilename;
    }
}
