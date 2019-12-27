import React, {Component} from 'react'
import {findDOMNode} from 'react-dom';
import {DragSource, DropTarget} from 'react-dnd';
import flow from 'lodash/flow';
import PropTypes from 'prop-types'
import Loading from "./Loading";

class Tile extends Component {

  getTileStyles = () => {
    return {
      border: '1px dashed gray',
      padding: '0.5rem 1rem',
      marginBottom: '.5rem',
      backgroundColor: 'white',
      display: 'inline-block',
      margin: '0 0.125rem',
      cursor: this.props.isDraggable ? 'move' : 'default',
      color: this.getColour(this.props.value),
    }
  };

  // colour up the types of value
  getColour = (value) => {
    // trim the string
    value = value.trim();

    // number
    if (!isNaN(value)) {
      return 'blue';
    }
    // operators
    if (value === "+" || value === "-" || value === "/" || value === "*") {
      return 'orange';
    }
    // equals sign
    if (value === "=") {
      return 'green';
    }
    // anything else
    return 'red';
  };

  render() {
    let {value} = this.props;
    const {isDragging, connectDragSource, connectDropTarget} = this.props;
    const opacity = isDragging ? 0.25 : 1;

    // convert empty values to spaces
    if (value.trim() === "") {
      value = "\u00A0";
    }

    // if is the loader tile
    // display the loader
    if (this.props.isLoader) {
      value = <Loading/>
    }

    // the template
    const jsx = <div style={{...this.getTileStyles(), opacity}}>
      {value}
    </div>;

    // if is draggable enable the dragging and dropping
    if (this.props.isDraggable) {
      return connectDragSource(connectDropTarget(
        jsx
      ))
    }
    else {
      return jsx;
    }
  }
}

const tileSource = {

  beginDrag(props) {
    return {
      index: props.index,
      value: props.value
    };
  },

  /**
   * when drag ends send recalculate the result
   * @param props
   * @param monitor
   */
  endDrag(props, monitor) {
    const item = monitor.getItem();
    const dropResult = monitor.getDropResult();
    props.calculateResult();
  }
};


const tileTarget = {

  hover(props, monitor, component) {
    const dragIndex = monitor.getItem().index;
    const hoverIndex = props.index;

    // Don't replace items with themselves
    if (dragIndex === hoverIndex) {
      return;
    }

    // Determine rectangle on screen
    const hoverBoundingRect = findDOMNode(component).getBoundingClientRect();

    // Get horizontal middle
    const hoverMiddleX = (hoverBoundingRect.right - hoverBoundingRect.left) / 2;


    // Determine mouse position
    const clientOffset = monitor.getClientOffset();

    // Get pixels to the right
    const hoverClientX = clientOffset.x - hoverBoundingRect.right;

    // Only perform the move when the mouse has crossed half of the horizontal

    // Dragging right
    if (dragIndex < hoverIndex && hoverClientX > hoverMiddleX) {
      return;
    }

    // Dragging left
    if (dragIndex > hoverIndex && hoverClientX > hoverMiddleX) {
      return;
    }

    props.moveTile(dragIndex, hoverIndex);

    // Note: we're mutating the monitor item here!
    // Generally it's better to avoid mutations,
    // but it's good here for the sake of performance
    // to avoid expensive index searches.
    monitor.getItem().index = hoverIndex;
  }
};

// propTypes
Tile.propTypes = {
  value: PropTypes.string.isRequired,
  isDraggable: PropTypes.bool,
  isLoader: PropTypes.bool,
};

export default flow(DragSource("TILE", tileSource, (connect, monitor) => ({
    connectDragSource: connect.dragSource(),
    isDragging: monitor.isDragging()
  })),
  DropTarget("TILE", tileTarget, connect => ({
    connectDropTarget: connect.dropTarget()
  })))(Tile);
