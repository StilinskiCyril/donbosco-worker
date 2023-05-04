/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

/**
 * Component Imports
 */
import ExampleComponent from './components/ExampleComponent.vue';
import TwoFactorAuthComponent from './components/TwoFactorAuthComponent.vue';
import SuperAdminDashboardComponent from './components/SuperAdminDashboardComponent.vue';
import AdminDashboardComponent from './components/AdminDashboardComponent.vue';
import TreasurerDashboardComponent from './components/TreasurerDashboardComponent.vue';
import UserDashboardComponent from './components/UserDashboardComponent.vue';

/**
 * Two-Factor Auth Component
 */
app.component('two-factor-auth-component', TwoFactorAuthComponent);

/**
 * Dashboard Components
 */
app.component('example-component', ExampleComponent);
app.component('super-admin-dashboard-component', SuperAdminDashboardComponent);
app.component('admin-dashboard-component', AdminDashboardComponent);
app.component('treasurer-dashboard-component', TreasurerDashboardComponent);
app.component('user-dashboard-component', UserDashboardComponent);

/**
 * Other Components
 */

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
