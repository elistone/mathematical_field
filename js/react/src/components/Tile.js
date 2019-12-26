import React, {Component} from 'react'
import {findDOMNode} from 'react-dom';
import {DragSource, DropTarget} from 'react-dnd';
import flow from 'lodash/flow';
import PropTypes from 'prop-types'

class Tile extends Component {

  getTileStyles = () => {
    return {
      border: '1px dashed gray',
      padding: '0.5rem 1rem',
      marginBottom: '.5rem',
      backgroundColor: 'white',
      cursor: this.props.isDraggable ? 'move' : 'default',
      display: 'inline-block'
    }
  };

  render() {
    const {value, isDragging, connectDragSource, connectDropTarget} = this.props;
    const opacity = isDragging ? 0.25 : 1;

    return connectDragSource(connectDropTarget(
      <div style={{...this.getTileStyles(), opacity}}>
        {value}
      </div>
    ))
  }
}

const tileSource = {

  beginDrag(props) {
    return {
      index: props.index,
      value: props.value
    };
  },

  endDrag(props, monitor) {
    const item = monitor.getItem();
    const dropResult = monitor.getDropResult();
    console.log('endDrag');
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
};

export default flow(DragSource("TILE", tileSource, (connect, monitor) => ({
    connectDragSource: connect.dragSource(),
    isDragging: monitor.isDragging()
  })),
  DropTarget("TILE", tileTarget, connect => ({
    connectDropTarget: connect.dropTarget()
  })))(Tile);
