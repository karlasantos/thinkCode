/**
 * Gulp: automatizador de tarefas
 * @type {*|Gulp}
 */
var gulp = require('gulp');

/**
 * Minify CSS
 * @type {*|(function(): stream)}
 */
var cssc = require('gulp-css-condense');

/**
 * Gulp Concat: concatena todos os arquivos do projeto
 */
var concat = require('gulp-concat');

/**
 * Minify JS
 */
var uglify = require('gulp-uglify');

/**
 * Concatena todos os arquivos de forma ordenada de acordo com a lógica
 */

var browserify = require('gulp-browserify');

/**
 * Renomeia os arquivos
 * @type {gulpRename}
 */
var rename = require('gulp-rename');

/**
 * Minificar as imagens
 */
const imagemin = require('gulp-imagemin');

/**
 * Tarefas executadas por padrão pelo comando gulp
 */
gulp.task('default', ['minify-css', 'minify-js', 'minify-img', 'minify-angular', 'watch']);

/**
 * Minificar os CSS do projeto e concatenar em um arquivo
 */
gulp.task('minify-css', function() {
    return gulp.src('css/*.css') //define o local dos arquivos para minificar
        .pipe(concat('all.min.css')) //concatena todos os min.css em um arquivo
        .pipe(cssc()) //minifica os arquivos
        .pipe(gulp.dest('css/minify')); //define o destino
});

/**
 * Minificar os JS do projeto e concatenar em um arquivo
 */
gulp.task('minify-js', function () {
    gulp.src('js/*.js') //define o local dos arquivos para minificar
        .pipe(concat('all.min.js')) //concatena todos os min.js em um arquivo
        .pipe(uglify()) //minifica os arquivos
        .pipe(gulp.dest('js/minify')); //define o destino
});

// gulp.task('minify-js', function () {
//     gulp.src('js/**/*.js') //define o local dos arquivos para minificar
//         .pipe(browserify()) //concatena todos os min.js em um arquivo
//         .pipe(uglify())
//         .pipe(rename('all.min.js')) //minifica os arquivos
//         .pipe(gulp.dest('js/minify')); //define o destino
// });

/**
 * Minificar os códigos com angular
 */
gulp.task('minify-angular', function () {
    gulp.src('js/app/app.js') //define o local dos arquivos para minificar
        .pipe(browserify()) //concatena todos os min.js em um arquivo
        // .pipe(uglify())
        .pipe(rename('all.min.js')) //minifica os arquivos
        .pipe(gulp.dest('js/app/minify')); //define o destino
});

// gulp.task('minify-angular', function () {
//     gulp.src('js/app/app.js') //define o local dos arquivos para minificar
//         .pipe(concat('all.min.js')) //concatena todos os min.js em um arquivo
//         .pipe(uglify()) //minifica os arquivos
//         .pipe(gulp.dest('js/app/minify')); //define o destino
// });

/**
 * Minificar as imagens do projeto
 */
gulp.task('minify-img', function () {
    gulp.src('img/*') //define o local dos arquivos para minificar
        .pipe(imagemin()) //minifica os arquivos
        .pipe(gulp.dest('img/minify')) //define o destino
});

/**
 * Observa as alterações do sistema e executa tarefas automaticamente
 * Ao rodar o comando gulp e modificar os arquivos do projeto ele fica observando cada alteração no projeto
 */
gulp.task('watch', function () {
    gulp.watch('css/*.css', ['minify-css']);
    gulp.watch('js/*.js', ['minify-js']);
    gulp.watch('img/*', ['minify-img']);
    gulp.watch('js/app/*.js', ['minify-angular']);
});