<?php

namespace App\Service;

use App\Entity\Partners;
use App\Entity\Prizes;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

class DataImportService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importPartners(string $filePath): void
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            // clean keys and values
            $cleanRecord = [];
            foreach ($record as $key => $value) {
                $cleanKey = trim($key);
                $cleanValue = trim($value);
                $cleanRecord[$cleanKey] = $cleanValue;
            }

            $partner = new Partners();
            $partner->setCsvPartnerId((int) $cleanRecord['id']);
            $partner->setName($cleanRecord['name']);
            $partner->setUrl($cleanRecord['url']);
            $partner->setPartnerCode($cleanRecord['code']);
            $partner->setLanguage(str_contains($cleanRecord['name'], 'en') ? 'en' : 'de');

            $this->entityManager->persist($partner);
        }

        $this->entityManager->flush();
    }

    public function importPrizes(string $filePath): void
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            // clean keys and values
            $cleanRecord = [];
            foreach ($record as $key => $value) {
                $cleanKey = trim($key);
                $cleanValue = trim($value);
                $cleanRecord[$cleanKey] = $cleanValue;
            }

            $language = str_contains($cleanRecord['name'], 'en') ? 'en' : 'de';

            $prize = new Prizes();
            $prize->setPrizeId((int) $cleanRecord['id']);
            $prize->setPrizeCode($cleanRecord['code']);
            $prize->setName($cleanRecord['name']);
            $prize->setLanguage($language);
            $prize->setAvailable(true);

            $partner = $this->entityManager->getRepository(Partners::class)->findBy(['partner_code' => $cleanRecord['partner_code'],
                'language' => $language])[0];
            $prize->setPartner($partner);

            $this->entityManager->persist($prize);
        }

        $this->entityManager->flush();
    }
}
