import ReactDOM from 'react-dom/client';
import './index.css';
import Main from './Main';
import React from 'react';

const rootElement = document.getElementById('app');

if (rootElement) {
  ReactDOM.createRoot(rootElement).render(
    <Main />
  );
} else {
  console.error('Root element with id "app" not found');
}
