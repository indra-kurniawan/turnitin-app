<?php

return [
    'avatar_column' => 'avatar_url',
    'disk' => env('FILESYSTEM_DISK', 'public'),
    'visibility' => 'public', // or replace by filesystem disk visibility with fallback value
    'show_custom_fields' => true,
    'custom_fields' => [
        'nim' => [
            'type' => 'text', // required
            'label' => 'NIM', // required
            'placeholder' => 'NIM Mahasiswa', // optional
            // 'id' => 'custom-field-1', // optional
            // 'required' => true, // optional
            // 'rules' => [], // optional
            // 'hint_icon' => '', // optional
            // 'hint' => '', // optional
            // 'suffix_icon' => '', // optional
            // 'prefix_icon' => '', // optional
            // 'default' => '', // optional
            // 'column_span' => 'full', // optional
            // 'autocomplete' => false, // optional
        ],
        'prodi' => [
            'type' => 'select', // required
            'label' => 'Program Studi', // required
            'placeholder' => 'Select', // optional
            'id' => 'custom-field-3', // optional
            'required' => true, // optional
            'options' => [
                'perbankan syariah' => 'Perbankan Syariah',
                'ekonomi syariah' => 'Ekonomi Syariah',
                'akuntansi syariah' => 'Akuntansi Syariah',
                'informatika' => 'Informatika',
                'bisnis digital' => 'Bisnis Digital',
                'sains data' => 'Sains Data',
            ], // optional
        ],
    ],

];
