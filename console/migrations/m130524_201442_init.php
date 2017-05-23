<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'anti_bruteforce_time' => $this->integer()->notNull(),
            'anti_bruteforce_count' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->batchInsert('{{%user}}', ['username', 'auth_key', 'password_hash',  'password_reset_token', 'email', 'anti_bruteforce_time', 'anti_bruteforce_count', 'status', 'created_at', 'updated_at'],
            [
                ['admin', 'WAnL0NUk0jlJfZ_q0YyaIESdrL9JuC_u', '$2y$13$sG3PEQz.pedxw1VBoFJLtOweqMC1Oxs073.OXdGEHe8jFs5SbQKGO', null, 'admin@admin.admin', 0,0, 10,  1486562879,1486562879],
                ['user', 'I3dCRlSsdNpTZ_3zJ3M6da6rl5SxGKhR', '$2y$13$sG3PEQz.pedxw1VBoFJLtOweqMC1Oxs073.OXdGEHe8jFs5SbQKGO',null, 'user@user.user', 0,0, 10,  1486562879,1486562879]
            ]);
    }


    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
