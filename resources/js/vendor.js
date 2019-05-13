// Vue
import Vue from 'vue';

// Syntax highlighting - Prism
import 'prismjs';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-php';

// Bind Vue, Popper and jQuery to window object
window.Vue = Vue;
window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');