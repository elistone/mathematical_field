import React from 'react'
import ReactDOM from 'react-dom'
import App from "./App";

const applications = document.getElementsByClassName('jumble-field');

for (let app of applications) {
  ReactDOM.render(
    <App dataset={app.dataset}/>, app
  );
}
