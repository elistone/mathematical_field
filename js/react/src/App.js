import React, {Component} from 'react'
import Calculation from "./components/Calculation";

class App extends Component {
  // fires before component is mounted
  constructor(props) {

    // makes this refer to this component
    super(props);

    // set local state
    this.state = {
      result: "0",
      equation: "11+22",
    };
  }

  render() {
    const {equation, result} = this.state;

    return (
      <div className="calculation">
        <Calculation equation={equation} result={result}/>
      </div>
    )
  }
}

export default App;
