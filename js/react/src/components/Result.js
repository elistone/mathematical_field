import React, {Component} from 'react'
import {DndProvider} from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import Tile from "./Tile";

class Result extends Component {

  render() {
    return (
      <div className="result">
        <DndProvider backend={HTML5Backend}>
          <Tile value={"="}/>
          {this.props.result.split("").map((value, i) => {
            return (<Tile key={i} value={value} isDraggable={false}/>)
          })}
        </DndProvider>
      </div>
    )
  }
}

export default Result;
