import React, {Component, useCallback} from 'react'
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
      equationArray: [],
    };
  }

  /**
   * On mount
   */
  componentDidMount() {
    // get the result
    this.getResult();
    // convert the equation to array
    this.equationToArray();
  }

  /**
   * Will at some point use the equation to get the correct result
   * at the moment creates a random number to prove a point
   */
  getResult() {
    const num = Math.floor(Math.random() * 999 + 1).toString();
    this.setState({
      result: num,
    });
  }

  /**
   * Converts the equation value to an array
   */
  equationToArray() {
    let output = [];

    // splits the string and creates a object that gets
    // added to an array
    this.state.equation.split("").map((value, i) => {
      let data = {
        id: i,
        value: value,
      };
      output.push(data);
    });

    // update the state
    this.setState({
      equationArray: output,
    });
  }

  moveTile = (dragIndex, hoverIndex) => {
    if (typeof dragIndex !== "undefined") {
      const dragTile = this.state.equationArray[dragIndex];
      let tilesOrder = this.state.equationArray;

      // remove item at drag index
      tilesOrder.splice(dragIndex, 1);
      // insert the new information into the order
      tilesOrder.splice(hoverIndex, 0, dragTile);

      // update the state
      this.setState({
        equationArray: tilesOrder,
      });
    }
  }

  // render setting a calculation
  render() {
    const {equationArray, result} = this.state;

    return (
      <div className="calculation">
        <Calculation equation={equationArray} result={result}
                     moveTile={this.moveTile}/>
      </div>
    )
  }
}

export default App;
