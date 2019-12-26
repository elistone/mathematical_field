import React, {Component} from 'react'
import Tile from "./Tile";

class Equation extends Component {

  render() {
    return (
      <div className="equation">
        {this.props.equation.split("").map((value, i) => {
          return (<Tile key={i} value={value} isDraggable={true}/>)
        })}
      </div>
    )
  }
}

export default Equation;
