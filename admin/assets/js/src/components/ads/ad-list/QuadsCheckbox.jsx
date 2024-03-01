import React from 'react';

class QuadsCheckbox extends React.Component {
  render() {
    const { index ,name ,value , onClick} = this.props;
    return (
      <div>
        <input
        className='quads_checkbox_adlist'
          type='checkbox'
          data-index={index}
          name={name}
          value={value}
          onClick={onClick}
        />
      </div>
    );
  }
}

export default QuadsCheckbox;
