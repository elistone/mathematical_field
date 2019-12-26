import React from 'react'
import ReactDOM from 'react-dom'
import App from "./App";

const app = document.getElementById('jumble-field');
ReactDOM.render(
  <App dataset={app.dataset}/>, app
);
