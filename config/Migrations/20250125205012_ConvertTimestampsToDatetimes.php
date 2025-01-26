<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class ConvertTimestampsToDatetimes extends AbstractMigration
{
    private $datetimeFields = [
        'projects' => [
            'created',
            'modified',
        ],
        'funding_cycles' => [
            'application_begin',
            'application_end',
            'vote_begin',
            'vote_end',
            'created',
            'modified',
            'resubmit_deadline',
        ],
        'messages' => [
            'created',
        ],
        'notes' => [
            'created',
        ],
        'users' => [
            'created',
            'modified',
        ],
        'votes' => [
            'created',
            'modified',
        ],
        'questions' => [
            'created',
            'modified',
        ],
        'answers' => [
            'created',
            'modified',
        ],
        'transactions' => [
            'created',
        ]
    ];

    public function up(): void
    {
        foreach ($this->datetimeFields as $table => $fields) {
            $table = $this->table($table);
            foreach ($fields as $field) {
                $table
                    ->changeColumn($field, 'datetime')
                    ->save();
            }
        }
    }

    public function down(): void
    {
        foreach ($this->datetimeFields as $table => $fields) {
            $table = $this->table($table);
            foreach ($fields as $field) {
                $table
                    ->changeColumn($field, 'timestamp')
                    ->save();
            }
        }
    }
}
