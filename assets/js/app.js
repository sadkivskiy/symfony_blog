// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

import Vue from 'vue';

import BlogApp from './components/BlogApp';

Vue.component('blog-app', BlogApp);

const app = new Vue({
    el: '#app'
});
