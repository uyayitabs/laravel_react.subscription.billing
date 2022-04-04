import React from "react";

class Content extends React.Component {
  constructor(props) {
    super(props)

    // console.log(props)

    this.handleError = this.handleError.bind(this)
  }

  handleError() {

  }
  
  render() {
    return(
      <div className="content">
        { this.props.children }
      </div>
    )
  }
}

export default Content;
