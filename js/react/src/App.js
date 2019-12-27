import React, {Component, useCallback} from 'react'
import axios from 'axios'
import Calculation from "./components/Calculation";

class App extends Component {

  // fires before component is mounted
  constructor(props) {

    // makes this refer to this component
    super(props);

    // set local state
    this.state = {
      result: "0",
      canDrag: false,
      equation: this.props.dataset.input,
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
  getResult = () => {
    this.setState({
      result: "Loading...",
      canDrag: false,
    });

    const equation = this.state.equation;
    const url = '/api/mathematical-field/calculation';
    const input = '?input=' + encodeURIComponent(equation);

    // TODO handle the errors
    axios.get(url + input)
      .then(response => {
        console.log("result", response.data.result.toString());
        this.setState({
          result: response.data.result.toString(),
          canDrag: true,
        });
      })
  };

  /**
   * Converts the equation value to an array
   */
  equationToArray = () => {
    let output = [];
    let string = "";

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
  };

  moveTile = (dragIndex, hoverIndex) => {
    if (typeof dragIndex !== "undefined") {
      const dragTile = this.state.equationArray[dragIndex];
      let tilesOrder = this.state.equationArray;

      // remove item at drag index
      tilesOrder.splice(dragIndex, 1);
      // insert the new information into the order
      tilesOrder.splice(hoverIndex, 0, dragTile);

      // convert the new tile order into a string
      const string = tilesOrder.reduce((output, tile) => {
        output.push(tile.value);
        return output;
      }, []).join("");

      // update the state
      this.setState({
        equation: string,
        equationArray: tilesOrder,
      });
    }
  };

  // render setting a calculation
  render() {
    const {equationArray, result, canDrag} = this.state;

    return (
      <div className="calculation">
        <Calculation equation={equationArray}
                     result={result}
                     canDrag={canDrag}
                     moveTile={this.moveTile}
                     calculateResult={this.getResult}/>
      </div>
    )
  }
}

export default App;
