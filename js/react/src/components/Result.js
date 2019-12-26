import React, {Component} from 'react'
import Tile from "./Tile";

class Result extends Component {

  render() {
    return (
      <div className="result">
        <Tile value={"="}/>
        {this.props.result.split("").map((value, i) => {
          return (<Tile key={i} value={value} isDraggable={false}/>)
        })}
      </div>
    )
  }
}

export default Result;
