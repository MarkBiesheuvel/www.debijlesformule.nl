const gulp = require('gulp')
const sourcemaps = require('gulp-sourcemaps')
const livereload = require('gulp-livereload')
const pump = require('pump')

gulp.task('css', (callback) => {

  // Plugins
  const rename = require('gulp-rename')
  const postcss = require('gulp-postcss')

  const processors = [
    require('cssnano')(),
    require('postcss-cssnext')(),
    require('postcss-nested')()
  ]

  pump([
      gulp.src('./src/css/theme.less'),
      sourcemaps.init(),
      postcss(processors),
      rename('theme.css'),
      sourcemaps.write('.'),
      gulp.dest('./dist'),
      livereload()
    ],
    callback
  )
})

gulp.task('js', (callback) => {

  // Plugins
  const babel = require('gulp-babel')
  const concat = require('gulp-concat')
  const uglify = require('gulp-uglify')

  pump([
      gulp.src('./src/js/*.js'),
      sourcemaps.init(),
      babel({ presets: ['es2015'] }),
      concat('script.js'),
      uglify(),
      sourcemaps.write('.'),
      gulp.dest('./dist'),
      livereload()
    ],
    callback
  )
})

gulp.task('html', (callback) => {

  const inline = require('gulp-inline')
  const htmlmin = require('gulp-htmlmin')
  const svgmin = require('gulp-svgmin')

  pump([
      gulp.src('./src/html/*.html'),
      inline({
        base: 'src/',
        svg: svgmin(),
        disabledTypes: ['img', 'css', 'js'],
      }),
      htmlmin({ collapseWhitespace: true }),
      gulp.dest('./dist'),
      livereload()
    ],
    callback
  )
})

gulp.task('img', (callback) => {

  pump([
      gulp.src('./src/images/**/*.*'),
      gulp.dest('./dist/images'),
      livereload()
    ],
    callback)
})

gulp.task('build', ['css', 'js', 'html', 'img'])

gulp.task('watch', () => {
  livereload.listen();
  gulp.watch('./src/css/*.less', ['css'])
  gulp.watch(['./src/html/*.html', './src/logo.svg'], ['html'])
  gulp.watch('./src/js/*.js', ['js'])
  gulp.watch('./src/images/**/*.*', ['img'])
})

gulp.task('default', ['build', 'watch'])
