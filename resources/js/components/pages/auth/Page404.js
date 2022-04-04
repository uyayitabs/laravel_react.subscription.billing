import React from "react";
import { Link } from "react-router-dom";

import { Button } from "reactstrap";

const Page404 = () => (
  <div className="text-center">
    <h1 className="display-1 font-weight-bold">404</h1>
    <p className="h1">Oops.</p>
    <p className="h2 font-weight-normal mt-3 mb-4">
      It seems there has been an error.
    </p>
    <Link to="/">
      <Button color="primary" size="lg">
        Return to website
      </Button>
    </Link>
  </div>
);

export default Page404;
