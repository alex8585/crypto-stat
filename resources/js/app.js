require('./bootstrap');

// Import modules...
import React from 'react';
import { render } from 'react-dom';
import { App } from '@inertiajs/inertia-react';
import { InertiaProgress } from '@inertiajs/progress';
import route from 'ziggy';
import { Ziggy } from './ziggy.js';
import { store } from './store.ts'
import { Provider } from 'react-redux'

const el = document.getElementById('app');

render(
    <Provider store={store}> 
        <App initialPage={JSON.parse(el.dataset.page)} resolveComponent={(name) => require(`./Pages/${name}`).default} />
    </Provider>,
    el
);

InertiaProgress.init({ color: '#4B5563' });
