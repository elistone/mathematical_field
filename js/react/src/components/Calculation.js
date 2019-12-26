import React, {Component} from 'react'
import Equation from "./Equation";
import Result from "./Result";

class Calculation extends Component {

  render() {
    return (
      <>
        <Equation equation={this.props.equation}/>
        <Result result={this.props.result}/>
      </>
    )
  }
}

export default Calculation;
