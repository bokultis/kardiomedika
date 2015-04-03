module.exports = function(grunt) {
    
    
    function getTplVars(mapValues){
        var result = {};
        for(var key in mapValues){
            result['{' + key + '}'] = mapValues[key];
        }
        return result;
    }
    
    var strtr = function (str, replacePairs) {
        var key, re;
        for (key in replacePairs) {
            if (replacePairs.hasOwnProperty(key)) {
                re = new RegExp(key, "g");
                str = str.replace(re, replacePairs[key]);
            }
        }
        return str;
    };
    
    var processFileVersion = function(content, srcpath){
        var d = new Date();
        var month = d.getMonth() + 1;
        var bn = d.getFullYear().toString() + month.toString() + d.getDate().toString() + d.getHours().toString();
        return content.replace(/\'built_number' => '(\d*)'/g, "'built_number' => '" + bn + "'");            
    };
    
    var config = grunt.file.readJSON('grunt.config.json');
    
    var uglifyConf = {};                    
    uglifyConf['../public/themes/' + config.theme.name + '/js/build.min.js'] = ['../public/themes/' + config.theme.name + '/js/build.js'];
    uglifyConf['../public/themes/' + config.theme.name + '/js/intro_build.min.js'] = ['../public/themes/' + config.theme.name + '/js/intro_build.js'];    
        
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            css: {
                src: [  '../public/plugins/bxSlider/bx_styles/jquery.bxslider.css', '../public/css/font-awesome.css',
                        '../public/plugins/fancybox/jquery.fancybox.css',
                        '../public/themes/' + config.theme.name + '/css/bootstrap.min.css',
                        '../public/themes/' + config.theme.name + '/css/bootstrap-form.css', '../public/themes/' + config.theme.name + '/css/bootstrap-grid.css',
                        '../public/themes/' + config.theme.name + '/css/fonts.css', '../public/themes/' + config.theme.name + '/css/style.css',
                        '../public/themes/' + config.theme.name + '/css/internal-pages.css', '../public/themes/' + config.theme.name + '/css/search.css',
                        '../public/themes/' + config.theme.name + '/css/sticky-footer.css'],
                dest: '../public/themes/' + config.theme.name + '/css/build.css'
            },
            js: {
                options: {
                    separator: ';'
                },
                src: [  '../public/js/head.js', '../public/js/jquery-1.11.1.min.js',
                '../public/plugins/picturefill/picturefill.js', '../public/plugins/bxSlider/jquery.bxSlider.js',
                '../public/plugins/fancybox/jquery.fancybox.pack.js',
                '../public/plugins/collapsibleBox/jquery.collapsibleBox.js', '../public/themes/' + config.theme.name + '/js/main.js',
                '../public/modules/contact/js/jquery.ajaxSubmit.js'],
                dest: '../public/themes/' + config.theme.name + '/js/build.js'                
            },
            intro_css: {
                src: [  '../public/css/font-awesome.css',
                        '../public/themes/' + config.theme.name + '/css/bootstrap-form.css', '../public/themes/' + config.theme.name + '/css/bootstrap-grid.css',
                        '../public/themes/' + config.theme.name + '/css/fonts.css', '../public/themes/' + config.theme.name + '/css/style.css',
                        '../public/themes/' + config.theme.name + '/css/intro.css'],
                dest: '../public/themes/' + config.theme.name + '/css/intro_build.css'
            },
            intro_js: {
                options: {
                    separator: ';'
                },                
                src: [  '../public/js/head.js', '../public/js/jquery-1.11.1.min.js',
                '../public/plugins/picturefill/picturefill.js', '../public/plugins/mousewheel/jquery.mousewheel.min.js',
                '../public/plugins/sequence/jquery.sequence-min.js',
                '../public/themes/' + config.theme.name + '/js/intro.js'],
                dest: '../public/themes/' + config.theme.name + '/js/intro_build.js'                
            }            
        },
        uglify: {
            options: {
                mangle: false
            },            
            dist: {
                files: uglifyConf
            }
        },
        cssmin : {
            css:{
                src: '../public/themes/' + config.theme.name + '/css/build.css',
                dest: '../public/themes/' + config.theme.name + '/css/build.min.css'
            },
            intro_css:{
                src: '../public/themes/' + config.theme.name + '/css/intro_build.css',
                dest: '../public/themes/' + config.theme.name + '/css/intro_build.min.css'
            }            
        },
        copy: {
            version: {
                src: '../application/version.php',
                dest: '../application/version.php',
                options: {
                    process: processFileVersion
                }              
            }          
        },
        exec:{
            git_pull: {
                cwd: '../',
                cmd: 'git pull'
            },            
            deploy: {
                cmd: function(mode, host) {                  
                    var modeParam = (mode == 'upload')? '': ' -n';
                    
                    if(!config.rsync.hosts[host]){
                        grunt.fail.fatal('[' + host + "] host not found in config file");
                    }                    
                    var hostConfig = config.rsync.hosts[host];
                    var command = hostConfig.command.replace('rsync', 'rsync' + modeParam);
                    grunt.log.writeln("Running deploy in mode:" + mode + " on host:" + host);
                    grunt.log.writeln("Command: " + command);
                    return command;
                }
            },
            git_push: {
                cwd: '../',
                cmd: 'git add .;git commit -m "grunt new build";git push'
            },
            dbrsync: {
                cmd: function(host) {                                      
                    if(!config.sshconfig[host]){
                        grunt.fail.fatal('[' + host + "] host not found in [sshconfig] of config file");
                    }                    
                    var hostConfig = config.sshconfig[host];
                    var commandTpl = "rsync -r -v --progress -c --rsh=\"ssh -p{port}\" dbupdates/ {username}@{host}:{remote_dir}/scripts/dbupdates/";
                    var command = strtr(commandTpl, getTplVars(hostConfig));
                    grunt.log.writeln("Running db scripts rsync on host:" + host);
                    grunt.log.writeln("Command: " + command);

                    return command;
                }                
            }
        },
        imagemin: {
            options: {
                cache: false
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: 'src/',
                    src: ['**/*.{png,jpg,gif}'],
                    dest: 'dist/'
                }]
            }
        },
        prompt: {
            ssh: {
                options: {
                    questions: [{
                        config: 'sshexec.dbup.options.password', // arbitrary name or config for any other grunt task
                        type: 'password', // list, checkbox, confirm, input, password
                        message: 'SSH Password', // Question to ask the user, function needs to return a string,
                        default: ''
                    }]
                }
            }
        },
        sshconfig: config.sshconfig,
        sshexec: {
            dbup: {
                command: 'php scripts/run.php -c cli -a dbupd -e development' + ((grunt.option("mode") === 'upload')?' -s no ':''),
                options: {
                    config: '<%= grunt.option("host") %>'
                }
            }
        }        
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-exec');
    grunt.loadNpmTasks('grunt-ssh');
    grunt.loadNpmTasks('grunt-prompt');    

    grunt.registerTask('default', ['concat', 'uglify', 'cssmin', 'copy']);
    
    grunt.registerTask('git', ['exec:git_pull', 'concat', 'uglify', 'cssmin', 'copy', 'exec:git_push']);
    
    var mode = grunt.option('mode') || 'simulation';
    grunt.option('mode', mode);
    var host = grunt.option('host') || 'staging';
    grunt.option('host', host);
    
    grunt.registerTask('deploy', ['exec:deploy:' + mode + ':' + host]);
    
    grunt.registerTask('dbdeploy', ['exec:dbrsync:' + host, 'prompt:ssh', 'sshexec:dbup']);

};