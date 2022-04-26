/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/app.scss';

// Chargement de la bibliothèque jQuery
const $ = require('jquery');

// Chargement de la partie JS de bootstrap
require('bootstrap');

// Chargement de la partie JF de Fontawesome
require('@fortawesome/fontawesome-free/js/all.js');

// MON JS
$(document).ready(function() {

    // Rend temporaire les div alerts (messages)
    if ($('div.alert')) {
        $('div.alert').delay(3500).fadeOut(350, function() {
            $('div.alert').remove();
        });

    }
});