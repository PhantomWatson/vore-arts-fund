import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import EditableFileInput from './EditableFileInput';

const rootElement = document.getElementById('image-uploader-root');
if (rootElement) {
  const root = ReactDOM.createRoot(rootElement);
  const images = window.preloadImages ?? [];

  root.render(
    <React.StrictMode>
      <App images={images} />
    </React.StrictMode>
  );
}

const editableInput = document.querySelector('[data-image-editor-input]');
if (editableInput) {
  const container = document.createElement('div');
  document.body.appendChild(container);

  ReactDOM.createRoot(container).render(
    <React.StrictMode>
      <EditableFileInput input={editableInput} />
    </React.StrictMode>
  );
}
