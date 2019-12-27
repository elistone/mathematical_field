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
      isLoading: false,
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
    const that = this;

    that.setState({
      result: "0",
      canDrag: false,
      isLoading: true,
    });

    const equation = this.state.equation;
    const url = '/api/mathematical-field/calculation';
    const input = '?input=' + encodeURIComponent(equation);

    // make a call to get the calculation
    axios.get(url + input)
      .then(response => {

        // if success update the status
        that.setState({
          result: response.data.result.toString(),
          hasError: false,
        });

      }).catch(function (error) {
      // handle the errors
      if (error.response) {
        const data = error.response.data;

        if (data) {
          // Request made and server responded
          that.setState({
            result: data.result.toString(),
          });
        }

      }
      else if (error.request) {
        // The request was made but no response was received
        console.error(error.request);
      }
      else {
        // Something happened in setting up the request that triggered an Error
        console.error('Error', error.message);
      }
    }).finally(function () {
      // change the is loading and can drag information
      that.setState({
        canDrag: true,
        isLoading: false,
      });

    });
  };

  /**
   * Converts the equation value to an array
   */
  equationToArray = () => {
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
    const {equationArray, result, canDrag, isLoading} = this.state;

    return (
      <div style={styles} className="calculation">
        <Calculation equation={equationArray}
                     result={result}
                     canDrag={canDrag}
                     isLoading={isLoading}
                     moveTile={this.moveTile}
                     calculateResult={this.getResult}/>
      </div>
    )
  }
}

const styles = {
  margin: "10px 0"
};

export default App;
