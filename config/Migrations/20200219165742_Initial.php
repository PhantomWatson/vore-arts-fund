<?php
use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{
    public function up()
    {

        $this->table('applications')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('category_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 2000,
                'null' => false,
            ])
            ->addColumn('amount_requested', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('accept_partial_payout', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('funding_cycle_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('status_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'category_id',
                ]
            )
            ->addIndex(
                [
                    'status_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('categories')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->create();

        $this->table('funding_cycles')
            ->addColumn('application_begin', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('application_end', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('vote_begin', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('vote_end', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('funding_available', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('images')
            ->addColumn('application_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('weight', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('caption', 'string', [
                'default' => '',
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'application_id',
                ]
            )
            ->create();

        $this->table('messages')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('application_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('body', 'string', [
                'default' => null,
                'limit' => 2000,
                'null' => false,
            ])
            ->addColumn('is_unread', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'application_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('notes')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('application_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('body', 'string', [
                'default' => null,
                'limit' => 2000,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'application_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('statuses')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->create();

        $this->table('users')
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('email', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('password', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => false,
            ])
            ->addColumn('phone', 'biginteger', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('is_admin', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('verification_code', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('is_verified', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('votes')
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('application_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('funding_cycle_id', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('weight', 'integer', [
                'default' => null,
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('created', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'limit' => null,
                'null' => false,
            ])
            ->addIndex(
                [
                    'application_id',
                ]
            )
            ->addIndex(
                [
                    'funding_cycle_id',
                ]
            )
            ->addIndex(
                [
                    'user_id',
                ]
            )
            ->create();

        $this->table('applications')
            ->addForeignKey(
                'category_id',
                'categories',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'status_id',
                'statuses',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('images')
            ->addForeignKey(
                'application_id',
                'applications',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('messages')
            ->addForeignKey(
                'application_id',
                'applications',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('notes')
            ->addForeignKey(
                'application_id',
                'applications',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();

        $this->table('votes')
            ->addForeignKey(
                'application_id',
                'applications',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'funding_cycle_id',
                'funding_cycles',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'update' => 'RESTRICT',
                    'delete' => 'RESTRICT'
                ]
            )
            ->update();
    }

    public function down()
    {
        $this->table('applications')
            ->dropForeignKey(
                'category_id'
            )
            ->dropForeignKey(
                'status_id'
            )
            ->dropForeignKey(
                'user_id'
            )->save();

        $this->table('images')
            ->dropForeignKey(
                'application_id'
            )->save();

        $this->table('messages')
            ->dropForeignKey(
                'application_id'
            )
            ->dropForeignKey(
                'user_id'
            )->save();

        $this->table('notes')
            ->dropForeignKey(
                'application_id'
            )
            ->dropForeignKey(
                'user_id'
            )->save();

        $this->table('votes')
            ->dropForeignKey(
                'application_id'
            )
            ->dropForeignKey(
                'funding_cycle_id'
            )
            ->dropForeignKey(
                'user_id'
            )->save();

        $this->table('applications')->drop()->save();
        $this->table('categories')->drop()->save();
        $this->table('funding_cycles')->drop()->save();
        $this->table('images')->drop()->save();
        $this->table('messages')->drop()->save();
        $this->table('notes')->drop()->save();
        $this->table('statuses')->drop()->save();
        $this->table('users')->drop()->save();
        $this->table('votes')->drop()->save();
    }
}
