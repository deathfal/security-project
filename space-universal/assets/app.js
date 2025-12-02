/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// Import Three.js
import * as THREE from 'three';
window.THREE = THREE;  // Make THREE available globally

// Import menu handlers
import './js/menu-handlers';

// Import cosmic background
import './js/cosmic-background';
