<?php

namespace Hcms;

use Composer\Script\Event;

class Installer {
    
    /**
     * singleton instance
     *
     * @var Hcms\Installer
     */
    protected static $instance = null;

    /**
     *
     * @var Composer\IO\IOInterface
     */
    protected $io;
    
    protected $event;
    
    /**
     *
     * @var array
     */
    protected $iniConfig = null;
    
    /**
     *
     * @var \PDO
     */
    protected $db = null;

    /**
     * private constructor
     */
    private function  __construct($event)
    {
        $this->io = $event->getIO();
        $this->event = $event;
        $this->dir = realpath(__DIR__ . "/../../");
    }    

    public static function preInstall(Event $event) {
        if(!isset(self::$instance )){
            self::$instance = new self($event);
        }
        return self::$instance->execPreInstall();
    }
    
    public static function postInstall(Event $event) {
        if(!isset(self::$instance )){
            self::$instance = new self($event);
        }
        return self::$instance->execPostInstall();
    }
    
    public static function postPackageInstall(Event $event)
    {
        if(!isset(self::$instance )){
            self::$instance = new self($event);
        }
        self::$instance->execPostPackageInstall($event->getOperation()->getPackage());
    }
    
    protected function execPostPackageInstall($installedPackage){
        if($installedPackage->getType() != 'horisen-cms_mod'){
            return true;
        }
        $extras = $installedPackage->getExtra();        
        if(!isset($extras['installer-name'])){
            return true;
        }
        $sqlFile = $this->dir . '/application/modules/' . $extras['installer-name'] . '/init.sql';
        if(!file_exists($sqlFile)){
            return true;
        }
        $sql = file_get_contents($sqlFile);
        if($this->execSql($sql)){
            $this->io->write("Sql executed for module " . $installedPackage->getName());
        }
    }    
    
    protected function execPostInstall(){    
        //remove .svn from packages
        $this->removeSVN($this->dir . '/application/modules/', false);
        $this->removeSVN($this->dir . '/library/', false);
        $this->removeSVN($this->dir . '/public/plugins/', false);
        $this->removeSVN($this->dir . '/public/modules/', false);
    }
    
    protected function execPreInstall(){
        //general
        if (!$this->io->askConfirmation("Do you want to run cms project setup? If no, we will just download and install packages?", true)) {
            return true;
        }
        //dist files
        if ($this->io->askConfirmation("Do you want to copy .dist files and create ini files? (application.ini, cli.ini, .htaccess) ", true)) {
            if($this->copyDistFiles(array(
                $this->dir . '/application/configs/dist.application.ini'    => $this->dir . '/application/configs/application.ini',
                $this->dir . '/application/configs/dist.cli.ini'    => $this->dir . '/application/configs/cli.ini',
                $this->dir . '/public/dist.htaccess'    => $this->dir . '/public/.htaccess'    
            ))){
                $this->io->write("Dist files copied");
            }
        }
        //db ini
        if ($this->io->askConfirmation("Do you want to update ini db settings? ", true)) {
            if($this->updateDbIniSettings()){
                $this->io->write("Ini db updated");
            }
        }
        //db create
        if ($this->io->askConfirmation("Do you want to create db tables and populate data? ", true)) {
            if($this->createDb()){
                $this->io->write("Db tables and data created");
            }
        }          
        //app log
        if ($this->io->askConfirmation("Do you want to create app log file? ", true)) {
            if($this->makeAppLog()){
                $this->io->write("App log created");
            }
        }
        //writtable
        if ($this->io->askConfirmation("Do you want to make folders writable? ", true)) {
            if($this->makeWritable(array(
                $this->dir . '/cache',
                $this->dir . '/logs/app.log',
                $this->dir . '/public/captcha',
                $this->dir . '/public/themes',
                $this->dir . '/public/content',
                $this->dir . '/tmp',
            ))){
                $this->io->write("Folders and files made writable");
            }
        } 
        //domain
        if ($this->io->askConfirmation("Do you want to make virtual domain? ", true)) {
            $domainName = $this->io->ask("Domain name? ", null);
            if($domainName){
                $newSyntax = $this->io->askConfirmation("Do you use new apache, >=2.4? ", false);
                if($this->makeVirtualDomain($this->dir, $domainName, $newSyntax)){
                    $this->io->write("Domain $domainName created");
                }
            }
        }
        $this->io->write("Init project finished.");
        return true;
    }
    
    protected function updateDbIniSettings(){
        $dbName = $this->io->ask("Db name? ", null);
        $dbUser = $this->io->ask("Db user? ", null);
        $dbPassword = $this->io->ask("Db password? ", null);
        $this->updateIniSettings(array(
            'resources.db.params.dbname = "wa_cms"'  => 'resources.db.params.dbname = "' . $dbName. '"',
            'resources.db.params.username = "root"'  => 'resources.db.params.username = "' . $dbUser. '"',
            'resources.db.params.password = "root"'  => 'resources.db.params.password = "' . $dbPassword. '"',
        ));
        $this->io->askConfirmation("Please create db named $dbName and then click enter to proceed!", true);
    }    
    
    protected function updateIniSettings($settings){    
        if(!is_array($settings) || count($settings) == 0){
            return false;
        }
        $iniFile = $this->dir . '/application/configs/application.ini';
        $fileContents = file_get_contents($iniFile);
        $fileContents = strtr($fileContents, $settings);
        file_put_contents($iniFile, $fileContents);
    }
    
    protected function loadAppIni(){
        $this->iniConfig = parse_ini_file($this->dir . '/application/configs/application.ini', true);

    }
    
    protected function createDb(){
        if(!isset($this->db)){
            if(!$this->openDb()){
                $this->io->write("Database tables cannot be created");
                return false;
            }
        }
        $sql = file_get_contents($this->dir . '/docs/db/scripts/init.sql');
        try {
            $this->db->exec($sql);
        } catch (\PDOException $e) {
            $this->io->write("Error executing init.sql with error: " . $e->getMessage());
            return false;
        }
        return true;
    }
    
    protected function execSql($sql){
        if(!isset($this->db)){
            if(!$this->openDb()){
                $this->io->write("sql cannot be executed");
                return false;
            }
        }
        try {
            $this->db->exec($sql);
        } catch (\PDOException $e) {
            $this->io->write("Error executing sql with error: " . $e->getMessage());
            return false;
        }
        return true;
    }    
    
    protected function openDb(){
        if(!isset( $this->iniConfig)){
            $this->loadAppIni();
        }
        $dbName = $this->iniConfig['production']['resources.db.params.dbname'];
        $dbHost = $this->iniConfig['production']['resources.db.params.host'];
        $dbUser = $this->iniConfig['production']['resources.db.params.username'];
        $dbPass = $this->iniConfig['production']['resources.db.params.password'];
        try {
            $this->db = new \PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
            /*foreach($this->db->query('SELECT * from application') as $row) {
                print_r($row);
            }*/            
        } catch (\PDOException $e) {
            $this->io->write("Error connecting to db $dbName as user $dbUser with error: " . $e->getMessage());
            return false;
        }        
        return true;        
    }
    
    protected function copyDistFile($from, $to){
        if(!copy($from, $to)){
            $this->io->write("Error copying file [$from] to [$to]");
            return false;
        }
        return true;
    }    

    protected function copyDistFiles($assoc) {
        $result = true;
        foreach ($assoc as $from => $to) {
            $result = $this->copyDistFile($from, $to) || $result;
        }
        return $result;
    }
    
    protected function makeAppLog(){
        $logFile = fopen($this->dir . "/logs/app.log", "w");
        if($logFile === false){
            $this->io->write("Error creating log file [logs/app.log]");
            return false;
        }
        else{
            fclose($logFile);    
        }
        return true;
    }

    protected function makeWritable($path) {
        if (is_array($path)) {
            foreach ($path as $currPath) {
                $this->makeWritable($currPath);
            }
        } else {
            shell_exec("sudo chmod -R 777 $path");
        }
        return true;
    }

    protected function makeVirtualDomain($projectDir, $domain, $newSyntax = false) {
        $public = $projectDir . '/public';

        if ($newSyntax) {
            $perm = "Require all granted";
        } else {
            $perm = "
       Order allow,deny
       Allow from all";
        }
        $apacheConf =
                "
<VirtualHost *:80>
   DocumentRoot \"$public\"
   ServerName $domain

   <Directory \"$public\">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       $perm
   </Directory>

  ErrorLog /var/log/apache2/error-$domain.log
</VirtualHost>
";
        $tmpFile = $projectDir . '/tmp.conf';
        file_put_contents($tmpFile, $apacheConf);
        shell_exec("sudo cp $tmpFile /etc/apache2/sites-available/$domain.conf");
        unlink($tmpFile);
        shell_exec("sudo a2ensite $domain.conf");
        shell_exec("sudo service apache2 reload");

        $this->io->write("Please add $domain entry in your hosts file");
        return true;
    }
    
    protected function removeSVN($dir, $checkRoot = true) {
        $svn = $dir . '.svn';
        if ($checkRoot && is_dir($svn)) {
            chmod($svn, 0777);
            $this->delTree($svn); // remove the .svn directory with a helper function
            if (is_dir($svn)){
                $this->io->write("Failed to delete $svn due to file permissions.");
            }
        }
        $handle = opendir($dir);
        while (false !== ( $file = readdir($handle) )) {
            if ($file == '.' || $file == '..'){
                continue;// don't get lost by recursively going through the current or top directory
            }                
            if (is_dir($dir . $file)){
                $this->removeSVN($dir . $file . '/'); // apply the SVN removal for sub directories
            }                
        }
    }

    
    protected function delTree($dir) {
        $files = glob($dir . '*', GLOB_MARK); // find all files in the directory
        foreach ($files as $file) {
            if (substr($file, -1) == '/'){
                $this->delTree($file); // recursively apply this to sub directories
            } else{
                unlink($file);
            }
        }
        if (is_dir($dir)) {
            rmdir($dir); // remove the directory itself (rmdir only removes a directory once it is empty)
        }
    }    

}