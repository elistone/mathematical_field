import React, {Component} from 'react'
import {DndProvider} from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import Tile from "./Tile";

class Result extends Component {

  render() {

    return (
      <div style={styles} className="result">
        <DndProvider backend={HTML5Backend}>
          <Tile value={"="}/>

          {/* if loading */}
          {this.props.isLoading ? (
            <Tile isLoader={true} isDraggable={false} value={""}/>
          ) : (
            this.props.result.split("").map((value, i) => {
              return (<Tile key={i} value={value} isDraggable={false}/>)
            })
          )}

        </DndProvider>
      </div>
    )
  }
}

const styles = {
  display: "inline-block"
};

export default Result;
