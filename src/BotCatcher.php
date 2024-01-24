<?php

namespace App;

use Cake\Routing\RouteBuilder;

class BotCatcher
{
    static public function connectBotRoutes(RouteBuilder &$builder)
    {
        // Bot-catcher
        $botCatcher = ['controller' => 'Pages', 'action' => 'botCatcher'];
        $paths = [
            '.svn',
            'addons',
            'admin/.git',
            'admin/SouthidcEditor',
            'admin/editor',
            'admin/start',
            'administrator',
            'advfile',
            'alimail',
            'api/.git',
            'app',
            'apps',
            'archive',
            'archiver',
            'asp.net',
            'auth',
            'back',
            'base',
            'bbs',
            'blog',
            'cgi',
            'ckeditor',
            'ckfinder',
            'clientscript',
            'cms',
            'common',
            'console',
            'core',
            'coremail',
            'CuteSoft_Client',
            'dialog',
            'docs',
            'editor',
            'examples',
            'extmail',
            'extman',
            'fangmail',
            'FCK',
            'fckeditor',
            'foosun',
            'forum',
            'help',
            'helpnew',
            'home',
            'ids/admin',
            'inc',
            'includes',
            'install',
            'issmall',
            'jcms',
            'ks_inc',
            'mail',
            'media',
            'new_gb',
            'next',
            'Ntalker',
            'phpmyadmin',
            'plug',
            'plugins',
            'prompt',
            'pub',
            'site',
            'siteserver',
            'skin',
            'system',
            'template',
            'themes',
            'tools',
            'tpl',
            'UserCenter',
            'wcm',
            'web2',
            'weblog',
            'whir_system',
            'wordpress',
            'wp',
            'wp-content',
            'wp-includes',
            'ycportal',
            'ymail',
            'zblog',
            'adminsoft',
        ];
        foreach ($paths as $path) {
            $builder->connect("/$path/*", $botCatcher);
        }
        $files = [
            'admin.php',
            'admin/index.php',
            'admin/login.asp',
            'admin/login.php',
            'app/login.jsp',
            'backup.sql.bz2',
            'bencandy.php',
            'data.sql',
            'db.sql',
            'db.sql.zip',
            'db.tar',
            'dbdump.sql.gz',
            'deptWebsiteAction.do',
            'docs.css',
            'doku.php',
            'dump.gz',
            'dump.sql',
            'dump.tar',
            'dump.tar.gz',
            'e/master/login.aspx',
            'Editor.js',
            'Error.aspx',
            'extern.php',
            'fckeditor.js',
            'feed.asp',
            'history.txt',
            'index.cgi',
            'kindeditor-min.js',
            'kindeditor.js',
            'lang/en.js',
            'License.txt',
            'list.php',
            'maintlogin.jsp',
            'master/login.aspx',
            'mysql.sql',
            'plugin.php',
            'site.sql',
            'sql.sql',
            'sql.tar.gz',
            'temp.sql',
            'User/Login.aspx',
            'wp-cron.php',
            'wp-login.php',
            'Wq_StranJF.js',
            'xmlrpc.php',
            'Search.html',
        ];
        foreach ($files as $file) {
            $builder->connect("/$file", $botCatcher);
        }
    }
}
