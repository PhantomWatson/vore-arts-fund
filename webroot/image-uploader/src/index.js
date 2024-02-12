import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';

const rootElement = document.getElementById('image-uploader-root');
const root = ReactDOM.createRoot(rootElement);
const images = window?.preloadImages;

root.render(
  <React.StrictMode>
    <App images={images} />
  </React.StrictMode>
);
