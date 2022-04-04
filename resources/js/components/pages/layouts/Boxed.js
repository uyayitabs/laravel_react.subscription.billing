import React from "react";
import { connect } from "react-redux";
import {
  enableBoxedLayout,
  disableBoxedLayout
} from "../../redux/actions/layoutActions";

import Dashboard from "../dashboards/Default";

class Boxed extends React.Component {
  componentDidMount() {
    const { dispatch } = this.props;
    dispatch(enableBoxedLayout());
  }

  componentWillUnmount() {
    const { dispatch } = this.props;
    dispatch(disableBoxedLayout());
  }

  render() {
    return <Dashboard />;
  }
}

export default connect()(Boxed);
