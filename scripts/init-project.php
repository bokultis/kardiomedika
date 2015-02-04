#!/usr/local/bin/php
<?php
$shortopts = ""; // Optional value

$longopts  = array(
    "directory::",
    "owner::",
    "domain::",
    "newsyntax::",
    "zenddir::",
    "help::"
);
$options = getopt($shortopts, $longopts);

if(isset($options['help'])){
    echo "
This tool can be used to init ZF1 projects.
It creates ini files, makes certain folders writtable by web server.
Optionally it can create and start apache domain for you.

Example:
php init-project.php --directory=/work/web-apps/cms/branches/mfa --domain=wa.cms.mfa --newsyntax

Available parameters

    --directory=projects directory
    --owner=webserver user:group name
    --domain=new virtual domain name
    --newsyntax=if domain is for apache >= 2.4
    --zenddir=path to the zend lib
    --help=show help   
";
    exit(0);
}

$directory = __DIR__;
if(isset($options['directory'])){
    $directory = $options['directory'];
    if(!is_dir($directory)){
        $directory = __DIR__ . DIRECTORY_SEPARATOR . $directory;
    }
}

$owner = false;
if(isset($options['owner'])){
    $owner = $options['owner'];
}

$domain = null;
if(isset($options['domain'])){
    $domain = $options['domain'];
}

$newDomainSyntax = false;
if(isset($options['newsyntax'])){
    $newDomainSyntax = true;
}

if(!is_dir($directory)){
    errorMessage("Directory [$directory] not found");
    exit(0);
}

$directory = realpath($directory);

echoMessage("Init project in directory [$directory]");

copyDistFiles(array(
    $directory . '/application/configs/dist.application.ini'    => $directory . '/application/configs/application.ini',
    $directory . '/application/configs/dist.cli.ini'    => $directory . '/application/configs/cli.ini',
    $directory . '/public/dist.htaccess'    => $directory . '/public/.htaccess'    
));

echoMessage ("Please add update your application.ini and cli.ini files with DB credentials.");

//make log
$logFile = fopen($directory . "/logs/app.log", "w");
if($logFile === false){
    errorMessage("Error creating log file [logs/app.log]");
}
else{
    fclose($logFile);    
}

makeDirs(array(
    $directory . '/cache',
    $directory . '/public/captcha',
    $directory . '/public/themes',
    $directory . '/public/content',
    $directory . '/tmp',    
));

makeWritable(array(
    $directory . '/cache',
    $directory . '/logs/app.log',
    $directory . '/public/captcha',
    $directory . '/public/themes',
    $directory . '/public/content',
    $directory . '/tmp',
), $owner);

//zend lib
if(isset($options['zenddir'])){
    shell_exec('ln -s "' . $options['zenddir']  . '" ' . $directory . '/library/Zend');
}

if($domain){
    makeVirtualDomain($directory, $domain, $newDomainSyntax);
}

echoMessage("Init project finished");




// ---- functions

function errorMessage($message){
    echo "\n$message!\n";
}

function echoMessage($message){
    echo "\n$message\n";
}

function copyDistFile($from, $to){
    if(!copy($from, $to)){
        errorMessage("Error copying file [$from] to [$to]");
    }
}

function copyDistFiles($assoc){
    foreach ($assoc as $from => $to) {
        copyDistFile($from, $to);
    }
}

function makeDirs(array $dirs){
    foreach ($dirs as $dir) {
        if(!is_dir($dir)){
            mkdir($dir);
        }
    }
}

function makeWritable($path, $owner){
    
    if(is_array($path)){
        foreach ($path as $currPath) {
            makeWritable($currPath, $owner);
        }
    }
    else{
        //make folder
        if(!$owner){
            shell_exec("sudo chmod -R 777 $path");
        } else{
            shell_exec("sudo chown -R $owner $path");
        }
        
        /*if($result == null){
            errorMessage("Error changing owner of the file [$path] to be $owner");
        }*/       
    }

}

function makeVirtualDomain($projectDir, $domain, $newSyntax = false){
    $public = $projectDir . '/public';
    
    if($newSyntax){
        $perm = "Require all granted";
    }
    else{
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
    
    echoMessage ("Please add $domain entry in your hosts file");
}