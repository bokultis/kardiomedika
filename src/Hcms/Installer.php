<?php

namespace Hcms;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

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
    
    protected $dbDir = '';

    /**
     * private constructor
     * @param Event $event
     */
    private function  __construct($event)
    {
        $this->io = $event->getIO();
        $this->event = $event;
        $this->dir = realpath(__DIR__ . "/../../");
        $this->dbDir = $this->dir . '/scripts/dbupdates';
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
    
    /**
     * 
     * @param \Composer\Script\Event $event
     */
    public static function prePackageInstall(PackageEvent $event)
    {
        if(!isset(self::$instance )){
            self::$instance = new self($event);
        }
        self::$instance->execPrePackageInstall($event);
    }
    
    /**
     * Execute Before Packages are installed
     */
    protected function execPrePackageInstall(){
        
    }    
    
    /**
     * 
     * @param \Composer\Script\Event $event
     */
    public static function postPackageInstall(PackageEvent $event)
    {
        if(!isset(self::$instance )){
            self::$instance = new self($event);
        }
        self::$instance->execPostPackageInstall($event->getOperation()->getPackage());
    }
    
    /**
     * Execute After Packages are installed
     * @param type $installedPackage
     * @return boolean
     */
    protected function execPostPackageInstall($installedPackage){
        if($installedPackage->getType() != 'horisen-cms_mod'){
            return true;
        }
        $extras = $installedPackage->getExtra(); 
        if(!isset($extras['installer-name'])){
            return true;
        }
        //make symlinks
        $this->makeSymlinkModulePublic($extras['installer-name']);
        $sqlFile = $this->dir . '/application/modules/' . $extras['installer-name'] . '/init.sql';
        if(!file_exists($sqlFile)){
            return true;
        }
        $this->copySqlScript($sqlFile, $extras['installer-name']);
        //$sql = file_get_contents($sqlFile);
        //if($this->execSql($sql)){
            //$this->io->write("Sql executed for module " . $installedPackage->getName());
        //}
    }
        
    /**
     * 
     * @param \Composer\Script\Event $event
     */
    public static function postPackageUpdate(PackageEvent $event)
    {
        if(!isset(self::$instance )){
            self::$instance = new self($event);
        }
        self::$instance->execPostPackageUpdate($event->getOperation()->getInitialPackage(),
                $event->getOperation()->getTargetPackage());
    }
    
    /**
     * Execute After Packages are installed
     * @param PackageInterface $initPackage
     * @param PackageInterface $targetPackage
     * @return boolean
     */
    protected function execPostPackageUpdate($initPackage, $targetPackage)
    {
        if($initPackage->getType() != 'horisen-cms_mod'){
            return true;
        }
        //$this->io->write("Updating version from: " . $initPackage->getVersion() . ", to: " . $targetPackage->getVersion());
        //$this->io->write("Updating version from: " . $initPackage->getPrettyVersion() . ", to: " . $targetPackage->getPrettyVersion());
        $extras = $initPackage->getExtra(); 
        if(!isset($extras['installer-name'])){
            return true;
        }
        $updatesDir = $this->dir . '/application/modules/' . $extras['installer-name'] . '/db_updates';
        //$updatesDir = __DIR__ . '/test_updates';
        $this->processUpdates($updatesDir, $initPackage->getPrettyVersion(), $targetPackage->getPrettyVersion(), $extras['installer-name']);
    }    
    
    protected function execPostInstall(){    
        //remove VCS from packages
        //$this->removeVCS($this->dir . '/application/modules/', 'git', false);
        //$this->removeVCS($this->dir . '/library/', 'git', false);
        //$this->removeVCS($this->dir . '/public/plugins/', 'git', false);
        //$this->removeVCS($this->dir . '/public/modules/', 'git', false);
    }
    
    protected function execPreInstall(){
        //general      
        if (!$this->io->askConfirmation("Do you want to run cms project setup? If no, we will just download and install packages?", true)) {
            return true;
        }
        
        //make ignored folders
        $this->makeDirs(array(
            $this->dir . '/cache',
            $this->dir . '/cache/core',
            $this->dir . '/cache/class',
            $this->dir . '/cache/file',
            $this->dir . '/public/captcha',
            $this->dir . '/public/modules',
            $this->dir . '/public/themes',
            $this->dir . '/public/content',
            $this->dir . '/logs',
            $this->dir . '/tmp'
        ));        

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
        if ($this->io->askConfirmation("Do you want to prepare db init scripts? ", true)) {
            if($this->createDb()){
                $this->io->write("Db init scripts prepared in [/scripts/dbupates] please run [composer dbup]");
            }
        }          
        //app log
        if ($this->io->askConfirmation("Do you want to create app log file? ", true)) {
            if($this->makeAppLog()){
                $this->io->write("App log created");
            }
        }
        $osDetected = php_uname('s');
        if(isset($osDetected) && $osDetected == "Linux"){
            //writtable
            if ($this->io->askConfirmation("Do you want to make folders writable? ", true)) {
                if($this->makeWritable(array(
                    $this->dir . '/cache',
                    $this->dir . '/logs/app.log',
                    $this->dir . '/public/captcha',
                    $this->dir . '/public/modules',
                    $this->dir . '/public/themes',
                    $this->dir . '/public/content',
                    $this->dir . '/tmp',
                ))){
                    $this->io->write("Folders and files made writable");
                }
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
        //grunt deplo
        if ($this->io->askConfirmation("Do you want to copy dist.grunt.config and prepare deploy configuration?", true)) {
            if($this->copyDistFile($this->dir . '/scripts/dist.grunt.config.json', $this->dir . '/scripts/grunt.config.json')){
                $this->io->write("grunt.config.json created");
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
            'resources.db.params.dbname = "wa_cms_genesis"'  => 'resources.db.params.dbname = "' . $dbName. '"',
            'resources.db.params.username = "root"'  => 'resources.db.params.username = "' . $dbUser. '"',
            'resources.db.params.password = "root"'  => 'resources.db.params.password = "' . $dbPassword. '"',
        ));
        $this->io->askConfirmation("Please create db named $dbName and then click enter to proceed!", true);
    }    
    
    protected function updateIniSettings($settings){    
        if(!is_array($settings) || count($settings) == 0){
            return false;
        }
        $iniFiles = array(
            $this->dir . '/application/configs/application.ini',
            $this->dir . '/application/configs/cli.ini',
        );
        foreach($iniFiles as $iniFile){
            $fileContents = file_get_contents($iniFile);
            $fileContents = strtr($fileContents, $settings);
            file_put_contents($iniFile, $fileContents);            
        }
    }
    
    protected function loadAppIni(){
        $this->iniConfig = parse_ini_file($this->dir . '/application/configs/application.ini', true);

    }
    
    protected function createDb(){
        
        $sqlFile = $this->dir . '/docs/db/scripts/init.sql';
        return $this->copySqlScript($sqlFile, 'skeleton');
        //$sql = file_get_contents($sqlFile);
        //return $this->execSql($sql);
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
    
    /**
     * Get update dir
     * 
     * @param string $updDir
     * @param string $fromVersion
     * @param string $toVersion
     * @param string $module
     * @return mixed|false
     */
    protected function processUpdates($updDir, $fromVersion, $toVersion, $module)
    {
        if(!is_dir($updDir)){
            return false;
        }
        //we don't know still how to process downgrades
        if(version_compare($fromVersion, $toVersion, '>=')){
            return false;
        }
        //find all sql files in the dir
        $files = glob($updDir . '/*.sql', GLOB_MARK); // find all files in the directory
        $updates = array();
        foreach ($files as $file) {
            $fileVersion = basename($file);
            if(version_compare($fileVersion, $fromVersion, '>') && version_compare($fileVersion, $toVersion, '<=')){
                $updates[] = $file;
            }
        }
        //sort updates
        usort($updates, function($a, $b){
           return version_compare($a, $b); 
        });
        
        foreach ($updates as $updateSql) {
            //$this->io->write("Executing db update [$updateSql]");
            //$this->execSql(file_get_contents($updateSql));
            $this->copySqlScript($updateSql, $module);
        }        
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
    
    protected function makeDirs(array $dirs){
        foreach ($dirs as $dir) {
            if(!is_dir($dir)){
                mkdir($dir);
            }
        }
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
    
    protected function removeVCS($dir, $type, $checkRoot = true) {
        $vcs = $dir . '.'. $type;
        if ($checkRoot && is_dir($vcs)) {
            chmod($vcs, 0777);
            $this->delTree($vcs); // remove the . directory with a helper function
            if (is_dir($vcs)){
                $this->io->write("Failed to delete $vcs due to file permissions.");
            }
        }
        $handle = opendir($dir);
        while (false !== ( $file = readdir($handle) )) {
            if ($file == '.' || $file == '..'){
                continue;// don't get lost by recursively going through the current or top directory
            }                
            if (is_dir($dir . $file)){
                $this->removeVCS($dir . $file . '/', $type); // apply the VCS removal for sub directories
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
    
    protected function makeDir($dir){
        if (!is_dir($dir)) {
            shell_exec("mkdir $dir");
            $this->io->write("$dir created.");
        }
    }
    
    protected function makeSymlinkModulePublic($module){
        
        $osDetected = php_uname('s');
        
        $from = $this->dir . '/application/modules/' . $module . '/public';
        $to = $this->dir . '/public/modules/' . $module;
        if (is_dir($from) && !file_exists($to)) {
            symlink($from, $to);
            $this->io->write("Symlink created on OS $osDetected.");
        }
    }
    
    /**
     * Get next sql script num
     * @return string
     */
    protected function getNextSqlNum()
    {
        $sqlFiles = glob($this->dbDir . '/*.sql', GLOB_MARK);
        return sprintf('%03d', count($sqlFiles) + 1);
    }
    
    protected function sqlScriptExists($scriptBaseName)
    {
        $sqlFiles = glob($this->dbDir . '/*' . $scriptBaseName, GLOB_MARK);
        return count($sqlFiles) > 0;        
    }
    
    /**
     * Copy sql script
     * @param string $fromFile
     * @param string $module
     * @return boolean
     */
    protected function copySqlScript($fromFile, $module)
    {
        $baseName = $this->getSqlScriptBaseName($fromFile, $module);
        if($this->sqlScriptExists($baseName)){
            return true;
        }
        $toFile = $this->dbDir . '/' . $this->getNextSqlNum() . '-' . $baseName;
        if(!copy($fromFile, $toFile)){
            $this->io->writeError("Error copying [$fromFile]");
            return false;
        }
        return true;
    }
    
    protected function getSqlScriptBaseName($file, $module)
    {
        return $module . '_' . basename($file);
    }
    

}