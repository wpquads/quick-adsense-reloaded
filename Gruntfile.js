/* local path: 
cd "P:\quick-adsense-reloaded\github\quick-adsense-reloaded"
server path: 
http://wpquads.com/wp-content/uploads/edd/2016/09/wp-quads-pro.zip
 * 
 */
module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        paths: {
            // Base destination dir free version for wordpress.org
            base: '../../wordpress-svn/tags/<%= pkg.version %>',
            basetrunk: '../../wordpress-svn/trunk/',
            basezip: '../../wordpress-svn/',
        },
        // Tasks here
        // Bump version numbers
        version: {
            css: {
                options: {
                    prefix: 'Version\\:\\s'
                },
                src: ['style.css']
            },
            php: {
                options: {
                    prefix: '\@version\\s+'
                },
                src: ['functions.php', '<%= pkg.name %>.php']
            }
        },
        // minify js
        uglify: {
            build: {
                files: [
                    //{'assets/js/quads-admin.min.js': 'assets/js/quads-admin.js'}
                    {'<%= paths.base %>/assets/js/quads-admin.min.js': 'assets/js/quads-admin.js'}
                ]
            }
        },
        // Copy to build folder
        copy: {
            build: {
                files: [
                    {expand: true, src: ['**', '!node_modules/**','!**/admin/assets/js/node_modules/**', '!Gruntfile.js', '!package.json', '!nbproject/**', '!grunt/**', '!wp-quads-pro.php', '!**/includes/admin/settings/advanced-settings.php', '!grafik/**', '!**/includes/admin/assets/js/src/components', '!**/includes/admin/assets/js/src/style', '!**/includes/admin/assets/js/src/style/index.jsx', '!**/includes/admin/assets/js/webpack.config.jsx', '!**/includes/admin/assets/js/package.jsx', '!**/includes/admin/assets/js/package-lock.jsx', '!**/includes/admin/assets/js/.babelrc'],
                        dest: '<%= paths.base %>'},
                    {expand: true, src: ['**', '!node_modules/**','!**/admin/assets/js/node_modules/**', '!Gruntfile.js', '!package.json', '!nbproject/**', '!grunt/**', '!wp-quads-pro.php', '!**/includes/admin/settings/advanced-settings.php', '!grafik/**', '!**/includes/admin/assets/js/src/components', '!**/includes/admin/assets/js/src/style', '!**/includes/admin/assets/js/src/style/index.jsx', '!**/includes/admin/assets/js/webpack.config.jsx', '!**/includes/admin/assets/js/package.jsx', '!**/includes/admin/assets/js/package-lock.jsx', '!**/includes/admin/assets/js/.babelrc'],
                        dest: '<%= paths.basetrunk %>'}
                ]
            },
        },
        'string-replace': {
            version: {
                files: {
                    '<%= paths.basetrunk %>quick-adsense-reloaded.php': 'quick-adsense-reloaded.php',
                    '<%= paths.base %>/quick-adsense-reloaded.php': 'quick-adsense-reloaded.php',
                    '<%= paths.base %>/readme.txt': 'readme.txt',
                    '<%= paths.basetrunk %>readme.txt': 'readme.txt',
                    
                },
                options: {
                    replacements: [{
                            pattern: /2.0.16/g,
                            replacement: '<%= pkg.version %>'
                        }]
                }
            }
        },
        // Clean the build folder
        clean: {
            options: {
                force: true
            },
            build: {
                files: [
                    {src: ['<%= paths.base %>']},
                    {src: ['<%= paths.basetrunk %>']},
                ]

            }
        },
        // Minify CSS files
        cssmin: {
            build: {
                files: [
                    //{'assets/css/quads-admin.min.css': 'assets/css/quads-admin.css'}
                    {'<%= paths.base %>/assets/css/quads-admin.min.css': 'assets/css/quads-admin.css'}
                ]
            }
        },
        // Compress the build folder into an upload-ready zip file
        compress: {
            build: {
                options:
                        {
                            archive: '<%= paths.basezip %>/quick-adsense-reloaded.zip'
                        },
                files:[
                    {
                    expand: true,
                    cwd: '<%= paths.base %>',
                    src: ['**/*']
                }
                ]
            }
        }


    });

    // Load all grunt plugins here
    // [...]
    //require('load-grunt-config')(grunt);
    require('load-grunt-tasks')(grunt);

    // Display task timing
    require('time-grunt')(grunt);

    // Build task
    //grunt.registerTask( 'build', [ 'compress:build' ]);
    // grunt.registerTask('build', ['clean:build', 'uglify:build', 'cssmin:build', 'copy:build', 'string-replace:version', 'compress:build']);
    grunt.registerTask('build', ['clean:build', 'copy:build','uglify:build', 'cssmin:build', 'string-replace:version', 'compress:build']);
};