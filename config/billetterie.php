<?php

return [
    // Les reversements se font après la fin de l'événement, plus ce nombre de jours
    'payout_delay_days' => (int) env('PAYOUT_DELAY_DAYS', 3),

    'contact' => [
        'whatsapp_number' => '2290161573573',
        'whatsapp_display' => '+229 01 61 57 35 73',
    ],

    'links' => [
        'instagram' => 'https://www.instagram.com/pheni_xagency',
        'tiktok' => 'https://www.tiktok.com/@phenixagency229',
    ],

    // Informations légales affichées dans les mentions légales (à remplir dans .env)
    'company' => [
        'name' => env('COMPANY_NAME', 'Phenix Agency'),
        'legal_form' => env('COMPANY_LEGAL_FORM'),
        'address' => env('COMPANY_ADDRESS'),
        'rccm' => env('COMPANY_RCCM'),
        'ifu' => env('COMPANY_IFU'),
        'email' => env('COMPANY_EMAIL'),
        'director' => env('COMPANY_DIRECTOR'),
        'host' => env('COMPANY_HOST'),
    ],
];
