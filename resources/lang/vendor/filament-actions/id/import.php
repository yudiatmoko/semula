<?php

return [

    'label' => 'Impor :label',

    'modal' => [

        'heading' => 'Impor :label',

        'form' => [

            'file' => [
                'label' => 'Berkas',
                'placeholder' => 'Unggah berkas Excel (.xlsx) atau CSV', 
                
                'rules' => [
                    'duplicate_columns' => '{0} Berkas tidak boleh memiliki lebih dari satu kolom header yang kosong.|{1,*} Berkas tidak boleh memiliki kolom header yang duplikat: :columns.',
                ],
            ],

            'columns' => [
                'label' => 'Kolom',
                'placeholder' => 'Pilih kolom',
            ],

        ],

        'actions' => [
            'download_example' => [
                'label' => 'Unduh contoh berkas Excel', 
            ],
            'import' => [
                'label' => 'Impor',
            ],
        ],

    ],

    'notifications' => [

        'completed' => [
            'title' => 'Impor selesai',
            'actions' => [
                'download_failed_rows_csv' => [
                    'label' => 'Unduh data baris yang gagal',
                ],
            ],
        ],

        'max_rows' => [
            'title' => 'Ukuran berkas terlalu besar',
            'body' => 'Anda tidak dapat mengimpor lebih dari :count baris sekaligus.',
        ],

        'started' => [
            'title' => 'Impor dimulai',
            'body' => 'Mulai mengimpor :count baris. Proses ini akan berjalan di latar belakang.',
        ],

    ],

    'example_csv' => [
        'file_name' => 'contoh-:importer',
    ],

    'failure_csv' => [
        'file_name' => 'impor-:import_id-:csv_name-gagal',
        'error_header' => 'kesalahan',
        'system_error' => 'Terjadi kesalahan sistem, harap hubungi tim support.',
        'column_mapping_required_for_new_record' => 'Kolom :attribute tidak dipetakan ke kolom dalam berkas, tetapi diperlukan untuk membuat data baru.',
    ],

];