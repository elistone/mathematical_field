import React, {Component} from 'react'
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
    return (
      <div style={this.getTileStyles()}>
        {this.props.value}
      </div>
    )
  }

}

// propTypes
Tile.propTypes = {
  value: PropTypes.string.isRequired,
  isDraggable: PropTypes.bool,
};


export default Tile;
