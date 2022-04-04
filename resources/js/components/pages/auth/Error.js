import React from "react";
import { Link } from "react-router-dom";

import { Button } from "reactstrap";

class Error extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    return(
      <div className="error-page">
        <p className="h1">Oops!</p>
        <h1 className="display-1 font-weight-bold">
          { this.props.error.status }
          {/* 500 */}
        </h1>
        <p className="h2 font-weight-normal mt-3 mb-4">
          { this.props.error.statusText }
          {/* Internal server error. */}
        </p>
        <Link to="/">
          <Button color="primary" size="lg">
            Return to Dashboard
          </Button>
        </Link>
      </div>
    )
  }
}

export default Error;
