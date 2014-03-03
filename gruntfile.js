module.exports = function(grunt) { // The “wrapper” function
  require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);
  // Automatically load dependencies
  grunt.initConfig({ // Project and task configuration
    pkg: grunt.file.readJSON('package.json'), // imports the JSON metadata stored in package.json
    uglify: {
      build: {
        files: {
          'JS/src/main.min.js': 'JS/src/main.js'
        }
      }
    },
    autoprefixer: {
      options: {
        browsers: ['> 1%', 'last 2 versions', 'ff >= 3.6', 'Opera >= 11.1']
      },
      build: {
        files: {
          'CSS/build/style.prefix.css': 'CSS/src/style.css'
        }
      }
    },
    concat: {
      css: {
        files: {
          'CSS/build/production.css': ['CSS/src/normalize.css', 'CSS/src/helper.css', 'Libraries/jquery-ui/jquery-ui-1.10.4.custom.min.css', 'CSS/build/style.prefix.css']
        }
      },
      jquery: {
        files: {
          'JS/build/jquery.js': ['Libraries/jquery/jquery-1.11.0.min.js', 'Libraries/jquery/jquery-migrate-1.2.1.min.js']
        }
      },
      ie8: {
        files: {
          'JS/build/ie8.js': ['Libraries/html5shiv/dist/html5shiv.js', 'Libraries/respond.min.js']
        }
      },
      js: {
        files: {
          'JS/build/production.js': ['Libraries/jquery-ui/jquery-ui-1.10.4.custom.min.js', 'JS/src/main.min.js']
        }
      }
    },
    cssc: {
      build: {
        options: {
          consolidateViaDeclarations: true,
          consolidateViaSelectors: true,
          consolidateMediaQueries: true
        },
        files: {
          'CSS/build/production.css': 'CSS/build/production.css'
        }
      }
    },
    cssmin: {
      build: {
        src: 'CSS/build/production.css',
        dest: 'CSS/build/production.css'
      }
    },
    imagemin: {
      dynamic: {
        files: [{
          expand: true,
          cwd: 'Images/src/',
          src: ['**/*.{png,jpg,gif}'],
          dest: 'Images/build/'
        }]
      }
    },
    watch: {
      options: {
        livereload: true
      },
      css: {
        files: ['CSS/src/*.css'],
        tasks: ['autoprefixer', 'cssc', 'concat:css'],
        options: {
          spawn: false,
        }
      },
      js: {
        files: ['JS/src/*.js'],
        tasks: ['uglify', 'concat:js'],
        options: {
          spawn: false,
        }
      },
      images: {
        files: ['Images/src/*.{png,jpg,gif}'],
        tasks: ['imagemin'],
        options: {
          spawn: false,
        }
      }
    }
  });
  grunt.registerTask('default', ['uglify', 'autoprefixer', 'concat', 'cssc:build', 'imagemin']); // Default tasks
};