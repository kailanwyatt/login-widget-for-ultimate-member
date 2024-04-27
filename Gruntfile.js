module.exports = function( grunt ) {

	'use strict';
	require('load-grunt-tasks')(grunt);
	var pkg = grunt.file.readJSON('package.json');
    var bannerTemplate = '/**\n' + ' * <%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %>\n' + ' * <%= pkg.author.url %>\n' + ' *\n' + ' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' + ' * Licensed GPLv2+\n' + ' */\n';
    var compactBannerTemplate = '/** ' + '<%= pkg.title %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> | <%= pkg.author.url %> | Copyright (c) <%= grunt.template.today("yyyy") %>; | Licensed GPLv2+' + ' **/\n';
	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		addtextdomain: {
			options: {
				textdomain: 'login-widget-for-ultimate-member',
			},
			update_all_domains: {
				options: {
					updateDomains: true
				},
				src: [ '*.php', '**/*.php', '!\.git/**/*', '!bin/**/*', '!node_modules/**/*', '!tests/**/*' ]
			}
		},

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt'
				}
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: [ '\.git/*', 'bin/*', 'node_modules/*', 'tests/*' ],
					mainFile: 'login-widget-for-ultimate-member.php',
					potFilename: 'login-widget-for-ultimate-member.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		makepot: {
            dist: {
                options: {
                    domainPath: '/languages/',
                    potFilename: pkg.name + '.pot',
                    type: 'wp-plugin'
                }
            }
        },
        addtextdomain: {
            dist: {
                options: { textdomain: pkg.name },
                target: {
                    files: {
                        src: [
                            '*.php',
                            '**/*.php',
                            '!node_modules/**',
                            '!tests/**'
                        ]
                    }
                }
            }
        },
        replace: {
            version_php: {
                src: [
                    '**/*.php',
                    '!vendor/**'
                ],
                overwrite: true,
                replacements: [
                    {
                        from: /Version:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
                        to: 'Version:$1' + pkg.version
                    },
                    {
                        from: /@version(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
                        to: '@version$1' + pkg.version
                    },
                    {
                        from: /@since(.*?)NEXT/gm,
                        to: '@since$1' + pkg.version
                    },
                    {
                        from: /VERSION(\s*?)=(\s*?['"])[a-zA-Z0-9\.\-\+]+/gm,
                        to: 'VERSION$1=$2' + pkg.version
                    }
                ]
            },
            version_readme: {
                src: 'README.md',
                overwrite: true,
                replacements: [{
                        from: /^\*\*Stable tag:\*\*(\s*?)[a-zA-Z0-9.-]+(\s*?)$/im,
                        to: '**Stable tag:**$1<%= pkg.version %>$2'
                    }]
            },
            readme_txt: {
                src: 'README.md',
                dest: 'release/' + pkg.version + '/readme.txt',
                replacements: [
                    {
                        from: /^# (.*?)( #+)?$/gm,
                        to: '=== $1 ==='
                    },
                    {
                        from: /^## (.*?)( #+)?$/gm,
                        to: '== $1 =='
                    },
                    {
                        from: /^### (.*?)( #+)?$/gm,
                        to: '= $1 ='
                    },
                    {
                        from: /^\*\*(.*?):\*\*/gm,
                        to: '$1:'
                    }
                ]
            }
        },
        copy: {
            release: {
                src: [
                    '**',
                    '!assets/js/components/**',
                    '!assets/css/sass/**',
					'!src/**',
                    '!assets/repo/**',
                    '!bin/**',
                    '!release/**',
                    '!tests/**',
                    '!node_modules/**',
                    '!**/*.md',
                    '!.travis.yml',
					'!*.dist',
                    '!.bowerrc',
                    '!.gitignore',
                    '!bower.json',
                    '!Dockunit.json',
                    '!Gruntfile.js',
                    '!package.json',
                    '!phpunit.xml',
                    '!yarn-error.log',
                    '!composer.json',
                    '!composer.lock',
                    '!package-lock.json',
                    '!yarn.lock'
                ],
                dest: 'release/' + pkg.version + '/'
            },
            svn: {
                cwd: 'release/<%= pkg.version %>/',
                expand: true,
                src: '**',
                dest: 'release/svn/'
            }
        },
        compress: {
            dist: {
                options: {
                    mode: 'zip',
                    archive: './release/<%= pkg.name %>.<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'release/<%= pkg.version %>',
                src: ['**/*'],
                dest: '<%= pkg.name %>'
            }
        },
        wp_deploy: {
            dist: {
                options: {
                    plugin_slug: '<%= pkg.name %>',
                    build_dir: 'release/svn/',
                    assets_dir: 'assets/repo/'
                }
            }
        },
        clean: {
            release: [
                'release/<%= pkg.version %>/',
                'release/svn/'
            ]
        },
	} );

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.registerTask( 'default', [ 'i18n','readme' ] );
	grunt.registerTask( 'i18n', ['addtextdomain', 'makepot'] );
	grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );

	grunt.registerTask('release', [
        'clean:release',
        'replace:readme_txt',
        'copy',
        'compress'
    ]);

	grunt.util.linefeed = '\n';

};
