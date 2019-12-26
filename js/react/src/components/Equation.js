import React, {Component} from 'react'
import {DndProvider} from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import Tile from "./Tile";

class Equation extends Component {

  render() {
    return (
      <div className="equation">
        <DndProvider backend={HTML5Backend}>
          {this.props.equation.map((e, i) => {
            return (
              <Tile key={i}
                    value={e.value}
                    index={i}
                    isDraggable={this.props.canDrag}
                    moveTile={this.props.moveTile}
                    calculateResult={this.props.calculateResult}/>)
          })}
        </DndProvider>
      </div>
    )
  }
}

export default Equation;
